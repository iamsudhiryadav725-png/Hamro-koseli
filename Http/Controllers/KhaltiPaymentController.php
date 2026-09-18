<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KhaltiPaymentController extends Controller
{
    /**
     * Kick off a Khalti ePayment for an order that was just created.
     */
    public function initiate(Order $order): RedirectResponse
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $secretKey = config('services.khalti.secret_key');

        if (! $secretKey) {
            Log::error('Khalti secret key is not configured.');

            return redirect()
                ->route('cart')
                ->withErrors(['payment' => 'Online payment is not configured yet. Please choose Cash on Delivery.']);
        }

        // ── Prevent initiating a new payment if already paid ──────────────────
        $existing = Payment::where('order_id', $order->id)->first();
        if ($existing && $existing->status === 'completed') {
            return redirect()
                ->route('order.confirmation', $order)
                ->with('info', 'This order has already been paid.');
        }

        $user = auth()->user();

        $response = Http::withHeaders([
            'Authorization' => 'Key '.$secretKey,
        ])->post(rtrim(config('services.khalti.base_url'), '/').'/epayment/initiate/', [
            'return_url' => route('khalti.callback'),
            'website_url' => config('app.url'),
            'amount' => (int) round($order->total_amount * 100),
            'purchase_order_id' => (string) $order->id,
            'purchase_order_name' => 'HamroKoseli Order #'.$order->id,
            'customer_info' => [
                'name' => $user->name,
                'email' => $user->email,
                // Phone lives on the shipping address (we no longer write it
                // back to users.phone to avoid the unique-constraint clash).
                'phone' => $order->shippingAddress?->phone ?? $user->phone ?? '',
            ],
        ]);

        if ($response->failed()) {
            Log::error('Khalti initiate failed', [
                'order_id' => $order->id,
                'status' => $response->status(),
            ]);

            return redirect()
                ->route('cart')
                ->withErrors(['payment' => 'Could not start the Khalti payment. Please try again or choose Cash on Delivery.']);
        }

        $data = $response->json();

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => $user->id,
                'gateway' => 'khalti',
                'total_amount' => $order->total_amount,
                'status' => 'pending',
                'reference_id' => $data['pidx'] ?? null,
            ]
        );

        return redirect()->away($data['payment_url']);
    }

    /**
     * Khalti redirects back here after the customer pays or cancels.
     * We verify server-side via Khalti's lookup API — never trust the
     * query string alone.
     */
    public function callback(Request $request): RedirectResponse
    {
        $pidx = $request->query('pidx');

        if (! $pidx) {
            return redirect()
                ->route('cart')
                ->withErrors(['payment' => 'Invalid payment response from Khalti.']);
        }

        $payment = Payment::where('reference_id', $pidx)->first();

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

        // ── Server-side verification with Khalti ──────────────────────────────
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Key '.config('services.khalti.secret_key'),
            ])
                ->timeout(15)
                ->retry(2, 2000)
                ->post(rtrim(config('services.khalti.base_url'), '/').'/epayment/lookup/', [
                    'pidx' => $pidx,
                ]);
        } catch (ConnectionException $e) {
            Log::error('Khalti lookup connection failed — server cannot reach Khalti', [
                'pidx' => $pidx,
                'order_id' => $payment->order_id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('cart')
                ->withErrors(['payment' => 'We could not verify your payment right now due to a network issue. Please contact support with your order #'.$payment->order_id.' and we will confirm it manually.']);
        }

        if ($response->failed()) {
            Log::error('Khalti lookup failed', [
                'pidx' => $pidx,
                'order_id' => $payment->order_id,
                'status' => $response->status(),
            ]);

            return redirect()
                ->route('cart')
                ->withErrors(['payment' => 'Could not verify payment with Khalti. Please contact support.']);
        }

        $data = $response->json();
        $status = $data['status'] ?? 'Failed';

        // ── Amount tampering check ────────────────────────────────────────────
        $returnedPaisa = $data['total_amount'] ?? 0;
        $expectedPaisa = (int) round($payment->total_amount * 100);

        if ($returnedPaisa !== $expectedPaisa) {
            Log::critical('Khalti amount mismatch — possible tampering', [
                'pidx' => $pidx,
                'order_id' => $payment->order_id,
                'expected_paisa' => $expectedPaisa,
                'returned_paisa' => $returnedPaisa,
            ]);

            $payment->markAsFailed();

            return redirect()
                ->route('cart')
                ->withErrors(['payment' => 'Payment verification failed. Please contact support.']);
        }

        // ── Finalise based on Khalti status ───────────────────────────────────
        if ($status === 'Completed') {
            $payment->markAsCompleted($data['transaction_id'] ?? $pidx);
            $payment->order->update(['status' => 'confirmed']);

            return redirect()
                ->route('order.confirmation', $payment->order)
                ->with('success', 'Payment successful! Your order has been placed.');
        }

        Log::warning('Khalti payment not completed', [
            'pidx' => $pidx,
            'order_id' => $payment->order_id,
            'status' => $status,
        ]);

        $payment->markAsFailed();

        return redirect()
            ->route('cart')
            ->withErrors(['payment' => "Khalti payment wasn't completed (status: {$status}). Please try again."]);
    }
}
