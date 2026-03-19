<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ThreeFactorConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $confirmUrl,
        public readonly string $userName,
        public readonly string $ipAddress,
        public readonly string $riskLevel,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Secure Login Confirmation — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.3fa-confirm',
        );
    }
}
