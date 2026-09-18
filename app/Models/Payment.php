<?php

namespace App\Models;

use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'gateway',
        'total_amount',
        'status',
        'transaction_id',
        'reference_id',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The order this payment is for (one-to-one).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * The user who made this payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------------------------------------------------------
    // Accessors / Helpers
    // -------------------------------------------------------------------------

    /**
     * Check whether the payment was successfully completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check whether the payment has been refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Mark the payment as completed and record the timestamp.
     */
    public function markAsCompleted(string $transactionId): bool
    {
        return $this->update([
            'status' => 'completed',
            'transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark the payment as failed.
     */
    public function markAsFailed(): bool
    {
        return $this->update(['status' => 'failed']);
    }

    /**
     * Mark the payment as refunded.
     */
    public function markAsRefunded(): bool
    {
        return $this->update(['status' => 'refunded']);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope by a specific status.
     */
    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to completed payments only.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to a specific payment gateway.
     */
    public function scopeViaGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saved(function ($payment) {
            if ($payment->wasRecentlyCreated || $payment->wasChanged('status')) {
                if ($payment->status !== 'pending' || $payment->wasChanged('status')) {
                    NotificationService::userPaymentStatusChanged($payment);
                }
            }
        });
    }
}
