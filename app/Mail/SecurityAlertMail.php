<?php

namespace App\Mail;

use App\Models\SecurityAlert;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecurityAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public SecurityAlert $alert;

    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(SecurityAlert $alert, User $user)
    {
        $this->alert = $alert;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjectPrefix = match ($this->alert->severity) {
            'critical' => '🚨 NGHIÊM TRỌNG:',
            'high' => '🔴 CAO:',
            'medium' => '🟡 TRUNG BÌNH:',
            'low' => '🔵 THẤP:',
            default => '⚠️',
        };

        return new Envelope(
            from: config('mail.from.address'),
            subject: "{$subjectPrefix} Cảnh báo an ninh tài khoản TechStore",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.alerts.security-alert',
            with: [
                'alert' => $this->alert,
                'user' => $this->user,
                'alertTypeLabel' => $this->alert->getAlertTypeLabel(),
                'severityBadge' => $this->alert->getSeverityBadge(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
