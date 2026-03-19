<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FaceAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $userName,
        public readonly string $userEmail,
        public readonly string $ipAddress,
        public readonly string $userAgent,
        public readonly string $attemptedAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 Security Alert: Unrecognized Face Login Attempt — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.face-alert',
        );
    }
}
