<?php

namespace App\Mail;

use App\Models\ReturnRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReturnStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ReturnRequest $return, public string $status) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->status) {
            'approved' => 'Yêu Cầu Hoàn Trả Được Phê Duyệt',
            'rejected' => 'Yêu Cầu Hoàn Trả Bị Từ Chối',
            'completed' => 'Hoàn Trả Đã Hoàn Thành',
            default => 'Cập Nhật Yêu Cầu Hoàn Trả',
        };

        return new Envelope(
            subject: $subject.' - Đơn Hàng #'.$this->return->order->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.return-status',
            with: [
                'return' => $this->return,
                'status' => $this->status,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
