<?php

namespace App\Mail;

use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewVendorRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Vendor $vendor) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Vendor Registration: '.$this->vendor->vendor_name,
        );
    }

    public function content(): Content
    {
        $adminBase = rtrim(config('app.admin_url', config('app.url')), '/');

        return new Content(
            view: 'emails.vendor-registered',
            with: [
                'vendor' => $this->vendor,
                'approveUrl' => $adminBase.'/vendors/'.$this->vendor->id.'/edit',
            ]
        );
    }
}
