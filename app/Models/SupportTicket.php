<?php

namespace App\Models;

use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'vendor_id',
        'ticket_number',
        'category',
        'subject',
        'description',
        'status',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saved(function ($ticket) {
            if ($ticket->wasRecentlyCreated || $ticket->wasChanged('status')) {
                NotificationService::vendorSupportTicketStatusChanged($ticket);
            }
        });
    }
}
