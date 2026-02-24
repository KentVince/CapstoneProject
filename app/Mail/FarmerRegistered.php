<?php

namespace App\Mail;

use App\Models\Farmer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class FarmerRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public string $defaultPassword = 'cafarm123';
    public ?string $qrCodePath = null;

    public function __construct(
        public Farmer $farmer,
    ) {
        // Resolve the full path to the QR code image
        if ($farmer->qr_code) {
            $path = Storage::disk('public')->path($farmer->qr_code);
            if (file_exists($path)) {
                $this->qrCodePath = $path;
            }
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'CAFARM - Your Account Has Been Registered',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.farmer-registered',
        );
    }
}
