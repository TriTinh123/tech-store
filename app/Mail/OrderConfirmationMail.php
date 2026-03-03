<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác Nhận Đặt Hàng #'.$this->order->id,
        );
    }

    public function content(): Content
    {
        $order = $this->order;
        $items = $order->items ?? [];

        return new Content(
            view: 'emails.order-confirmation',
            with: [
                'order' => $order,
                'items' => $items,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
