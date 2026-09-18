<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EsewaPaymentController extends Controller
{
    /**
     * eSewa doesn't have a "create session, get a URL back" API like
     * Khalti — instead you build a signed HTML form and the customer's
     * browser POSTs it straight to eSewa. This shows a tiny page that
     * auto-submits that form.
     */
    public function initiate(Order $order): View
    {
        abort_if($order->user_id !== auth()->id(), 403);

        // ── Block re-initiation if already paid ───────────────────────────────
        $existing = Payment::where('order_id', $order->id)->first();
        if ($existing && $existing->status === 'completed') {
            return redirect()
                ->route('order.confirmation', $order)
                ->with('info', 'This order has already been paid.');
        }

        $config = config('services.esewa');

        // ── Always generate a fresh UUID per attempt ──────────────────────────
        // Timestamp-based UUIDs collide on fast retries and eSewa rejects
        // duplicates. A random UUID guarantees uniqueness every time.
        $transactionUuid = $order->id.'-'.Str::uuid();

        $totalAmount = number_format((float) $order->total_amount, 2, '.', '');

        $signedFieldNames = 'total_amount,transaction_uuid,product_code';
        $message = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$config['product_code']}";
        $signature = base64_encode(hash_hmac('sha256', $message, $config['secret_key'], true));

        // ── Save this attempt — always INSERT a new row, never reuse old UUID ─
        // We delete any previous pending payment for this order first so we
        // don't accumulate stale rows, but completed payments are never touched.
        Payment::where('order_id', $order->id)
            ->where('status', 'pending')
            ->delete();

        Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'gateway' => 'esewa',
            'total_amount' => $order->total_amount,
            'status' => 'pending',
            'reference_id' => $transactionUuid,
        ]);

        return view('esewa-redirect', [
            'formUrl' => $config['form_url'],
            'fields' => [
                'amount' => $totalAmount,
                'tax_amount' => '0',
                'total_amount' => $totalAmount,
                'transaction_uuid' => $transactionUuid,
                'product_code' => $config['product_code'],
                'product_service_charge' => '0',
                'product_delivery_charge' => '0',
                'success_url' => route('esewa.callback'),
                'failure_url' => route('esewa.callback'),
                'signed_field_names' => $signedFieldNames,
                'signature' => $signature,
            ],
        ]);
    }

    /**
     * eSewa redirects the customer's browser back here (both on success
     * and failure) with a base64-encoded `data` query param. We never
     * trust that on its own — we re-verify with eSewa's status API first.
     */
    public function callback(Request $request)
    {
        $encoded = $request->query('data');

        if (! $encoded) {
            return redirect()
                ->route('cart')
                ->withErrors(['payment' => 'Invalid payment response from eSewa.']);
        }

        $decoded = json_decode(base64_decode($encoded), true);

        if (! is_array($decoded) || empty($decoded['transaction_uuid'])) {
            return redirect()
                ->route('cart')
                ->withErrors(['payment' => 'Invalid payment response from eSewa.']);
        }

        $payment = Payment::where('reference_id', $decoded['transaction_uuid'])->first();

        abort_if(! $payment, 404);
        abort_if($payment->user_id !== auth()->id(), 403);

        // ── Replay attack guard ───────────────────────────────────────────────
        if ($payment->status === 'completed') {
            return redirect()
                ->route('order.confirmation', $payment->order)
                ->with('info', 'This payment has already been processed.');
        }

        if ($payment->status === 'failed') {
            return redirect()
                ->route('cart')
                ->withErrors(['payment' => 'This payment already failed. Please start a new checkout.']);
        }

        $config = config('services.esewa');

        // ── Server-to-server confirmation ─────────────────────────────────────
        try {
            $response = Http::timeout(15)->get($config['status_url'], [
                'product_code' => $config['product_code'],
                'total_amount' => number_format((float) $payment->total_amount, 2, '.', ''),
                'transaction_uuid' => $decoded['transaction_uuid'],
            ]);
        } catch (ConnectionException $e) {
            Log::error('eSewa status check connection failed', [
                'order_id' => $payment->order_id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('cart')
                ->withErrors(['payment' => 'We could not verify your payment right now due to a network issue. Please contact support with your order #'.$payment->order_id.' and we will confirm it manually.']);
        }

        if ($response->failed()) {
            Log::error('eSewa status check failed', [
                'order_id' => $payment->order_id,
                'status' => $response->status(),
            ]);

            return redirect()
                ->route('cart')
                ->withErrors(['payment' => 'Could not verify the eSewa payment. Please contact support.']);
        }

        $data = $response->json();
        $status = $data['status'] ?? null;

        // ── Amount tampering check ────────────────────────────────────────────
        $returnedAmount = (float) ($data['total_amount'] ?? 0);
        $expectedAmount = (float) $payment->total_amount;

        if (abs($returnedAmount - $expectedAmount) > 0.01) {
            Log::critical('eSewa amount mismatch — possible tampering', [
                'order_id' => $payment->order_id,
                'expected_amount' => $expectedAmount,
                'returned_amount' => $returnedAmount,
            ]);

            $payment->markAsFailed();

            return redirect()
                ->route('cart')
                ->withErrors(['payment' => 'Payment verification failed. Please contact support.']);
        }

        // ── Finalise based on eSewa status ────────────────────────────────────
        if ($status === 'COMPLETE') {
            $payment->markAsCompleted($data['ref_id'] ?? $decoded['transaction_uuid']);
            $payment->order->update(['status' => 'confirmed']);

            return redirect()
                ->route('order.confirmation', $payment->order)
                ->with('success', 'Payment successful! Your order has been placed.');
        }

        Log::warning('eSewa payment not completed', [
            'order_id' => $payment->order_id,
            'status' => $status,
        ]);

        $payment->markAsFailed();

        return redirect()
            ->route('cart')
            ->withErrors(['payment' => "eSewa payment wasn't completed (status: {$status}). Please try again."]);
    }
}
