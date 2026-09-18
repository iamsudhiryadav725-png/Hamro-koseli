<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    public $timestamps = false; // only has created_at, no updated_at

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // ─── Notification type constants ──────────────────────────────────────────

    const TYPE_ORDER_PLACED = 'order_placed';

    const TYPE_PAYMENT_RECEIVED = 'payment_received';

    const TYPE_ORDER_CONFIRMED = 'order_confirmed';

    const TYPE_ORDER_SHIPPED = 'order_shipped';

    const TYPE_ORDER_DELIVERED = 'order_delivered';

    const TYPE_ORDER_CANCELLED = 'order_cancelled';

    const TYPE_RETURN_REQUESTED = 'return_requested';

    const TYPE_RETURN_APPROVED = 'return_approved';

    const TYPE_PAYOUT_PROCESSED = 'payout_processed';

    const TYPE_PROFILE_UPDATED = 'profile_updated';

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * The user who receives this notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Scope -only unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope -only read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }
}
