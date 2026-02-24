<?php

namespace App\Mail;

use App\Models\AgriculturalProfessional;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProfessionalRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public string $defaultPassword = 'cafarm123';
    public ?string $qrCodePath = null;

    public function __construct(
        public AgriculturalProfessional $professional,
    ) {
        if ($professional->qr_code) {
            $path = Storage::disk('public')->path($professional->qr_code);
            if (file_exists($path)) {
                $this->qrCodePath = $path;
            }
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'CAFARM - Your Professional Account Has Been Registered',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.professional-registered',
        );
    }
}
