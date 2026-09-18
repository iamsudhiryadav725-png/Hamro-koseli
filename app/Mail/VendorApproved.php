<?php

namespace App\Mail;

use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorApproved extends Mailable
{
    use Queueable, SerializesModels;

    public Vendor $vendor;

    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Your Vendor Account Has Been Approved - Hamrokoseli',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.vendor-approved',
        );
    }
}
