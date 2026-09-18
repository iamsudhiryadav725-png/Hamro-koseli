<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Vendor;

class NotificationService
{
    /**
     * Call this right after a COD order is saved, or after eSewa/Khalti
     * confirm payment. Reads directly from the orders + order_items rows
     * that were just written — no extra queries beyond what's needed.
     */
    public static function orderPlaced(Order $order): void
    {
        // Load only what we need: the buyer's name, and each item's
        // vendor_id + product name + quantity + subtotal
        $order->loadMissing(['user', 'orderItems.product']);

        $customerName = $order->user->name;
        $paymentMethod = match ($order->payment_method) {
            'cod' => 'Cash on Delivery',
            'esewa' => 'eSewa',
            'khalti' => 'Khalti',
            default => $order->payment_method,
        };

        // One notification per vendor whose items are in this order
        foreach ($order->orderItems->groupBy('vendor_id') as $vendorId => $items) {

            $vendor = Vendor::find($vendorId);
            if (! $vendor?->user_id) {
                continue;
            }

            Notification::create([
                'user_id' => $vendor->user_id,
                'type' => Notification::TYPE_ORDER_PLACED,
                'title' => 'New Order Received',
                // Store the exact fields we want to show — pulled straight
                // from the order row that was just inserted
                'message' => json_encode([
                    'order_ref' => '#HK-'.str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    'customer_name' => $customerName,
                    'payment_method' => $paymentMethod,
                    'amount' => 'Rs. '.number_format($items->sum('subtotal'), 2),
                    'quantity' => $items->sum('quantity'),
                    'products' => $items->map(fn ($i) => $i->product->name)->filter()->implode(', '),
                    'order_status' => $order->status,          // 'pending'
                    'placed_at' => $order->created_at->format('M j, Y  g:i A'),
                ]),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Call this after eSewa/Khalti confirm payment (after orderPlaced).
     * Creates a second "Payment Received" notification with confirmed status.
     */
    public static function paymentConfirmed(Order $order): void
    {
        $order->loadMissing(['user', 'orderItems.product', 'payment']);

        $customerName = $order->user->name;
        $gateway = match (strtolower($order->payment?->gateway ?? '')) {
            'esewa' => 'eSewa',
            'khalti' => 'Khalti',
            default => ucfirst($order->payment_method),
        };

        foreach ($order->orderItems->groupBy('vendor_id') as $vendorId => $items) {

            $vendor = Vendor::find($vendorId);
            if (! $vendor?->user_id) {
                continue;
            }

            Notification::create([
                'user_id' => $vendor->user_id,
                'type' => 'vendor_payment_received',
                'title' => 'Payment Received',
                'message' => json_encode([
                    'order_ref' => '#HK-'.str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    'customer_name' => $customerName,
                    'payment_method' => $gateway,
                    'amount' => 'Rs. '.number_format($items->sum('subtotal'), 2),
                    'quantity' => $items->sum('quantity'),
                    'products' => $items->map(fn ($i) => $i->product->name)->filter()->implode(', '),
                    'order_status' => 'confirmed',
                    'placed_at' => $order->created_at->format('M j, Y  g:i A'),
                ]),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Notify the buyer/user when their order status is changed.
     */
    public static function userOrderStatusChanged(Order $order): void
    {
        $order->loadMissing('user');

        $orderRef = '#HK-'.str_pad($order->id, 5, '0', STR_PAD_LEFT);

        $title = match ($order->status) {
            'confirmed' => 'Order Confirmed',
            'shipped' => 'Order Shipped',
            'delivered' => 'Order Delivered',
            'cancelled' => 'Order Cancelled',
            default => 'Order Status Updated',
        };

        $message = match ($order->status) {
            'confirmed' => "Your order {$orderRef} has been confirmed.",
            'shipped' => "Your order {$orderRef} has been shipped. It's on its way!",
            'delivered' => "Your order {$orderRef} has been delivered. Thank you for shopping with us!",
            'cancelled' => "Your order {$orderRef} has been cancelled.",
            default => "Your order {$orderRef} status has been updated to {$order->status}.",
        };

        $type = match ($order->status) {
            'confirmed' => Notification::TYPE_ORDER_CONFIRMED,
            'shipped' => Notification::TYPE_ORDER_SHIPPED,
            'delivered' => Notification::TYPE_ORDER_DELIVERED,
            'cancelled' => Notification::TYPE_ORDER_CANCELLED,
            default => Notification::TYPE_ORDER_PLACED,
        };

        Notification::create([
            'user_id' => $order->user_id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /**
     * Notify the buyer/user when their payment status is changed by the seller.
     */
    public static function userPaymentStatusChanged(Payment $payment): void
    {
        $payment->loadMissing(['order', 'user']);

        $orderRef = $payment->order
            ? '#HK-'.str_pad($payment->order->id, 5, '0', STR_PAD_LEFT)
            : 'N/A';

        $title = match ($payment->status) {
            'completed' => 'Payment Received',
            'failed' => 'Payment Failed',
            'refunded' => 'Payment Refunded',
            default => 'Payment Status Updated',
        };

        $message = match ($payment->status) {
            'completed' => 'Your payment of Rs. '.number_format($payment->total_amount, 2)." for order {$orderRef} has been completed successfully.",
            'failed' => "Your payment for order {$orderRef} has failed.",
            'refunded' => "Your payment for order {$orderRef} has been refunded.",
            default => "Your payment status for order {$orderRef} has been updated to {$payment->status}.",
        };

        $type = match ($payment->status) {
            'completed' => Notification::TYPE_PAYMENT_RECEIVED,
            'refunded' => Notification::TYPE_RETURN_APPROVED,
            default => Notification::TYPE_PAYMENT_RECEIVED,
        };

        Notification::create([
            'user_id' => $payment->user_id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /**
     * Notify the user when they update their profile or password.
     */
    public static function profileUpdated(User $user, string $type = 'profile', bool $isVendor = false): void
    {
        $message = $type === 'password'
            ? 'Your account password has been updated successfully.'
            : 'Your profile details have been updated successfully.';

        Notification::create([
            'user_id' => $user->id,
            'type' => $isVendor ? 'vendor_profile_updated' : Notification::TYPE_PROFILE_UPDATED,
            'title' => $type === 'password' ? 'Password Updated' : 'Profile Updated',
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /**
     * Notify the vendor/seller when their payout status is changed.
     */
    public static function vendorPayoutStatusChanged(Payout $payout): void
    {
        $payout->loadMissing('vendor');
        if (! $payout->vendor?->user_id) {
            return;
        }

        $title = match ($payout->status) {
            'completed' => 'Payout Completed',
            'processing' => 'Payout Processing',
            'failed' => 'Payout Failed',
            default => 'Payout Status Updated',
        };

        $message = match ($payout->status) {
            'completed' => 'Your payout of Rs. '.number_format($payout->amount, 2)." has been completed successfully via {$payout->method}.",
            'processing' => 'Your payout of Rs. '.number_format($payout->amount, 2).' is now processing.',
            'failed' => 'Your payout of Rs. '.number_format($payout->amount, 2).' has failed.',
            default => "Your payout status has been updated to {$payout->status}.",
        };

        Notification::create([
            'user_id' => $payout->vendor->user_id,
            'type' => 'payout_processed',
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /**
     * Notify the vendor/seller when their support ticket status is changed.
     */
    public static function vendorSupportTicketStatusChanged(SupportTicket $ticket): void
    {
        $ticket->loadMissing('vendor');
        if (! $ticket->vendor?->user_id) {
            return;
        }

        $title = 'Support Ticket Update';
        $message = "Your support ticket {$ticket->ticket_number} status has been updated to '{$ticket->status}'.";

        Notification::create([
            'user_id' => $ticket->vendor->user_id,
            'type' => 'support_ticket_status',
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }
}
