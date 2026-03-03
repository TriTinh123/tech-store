<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Mail\OrderConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderCreated $event): void
    {
        // Load user and items
        $order = $event->order;

        if (! $order->customer_email) {
            return; // Skip if no customer email
        }

        try {
            Mail::to($order->customer_email)->send(
                new OrderConfirmationMail($order)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send order confirmation email: '.$e->getMessage());
        }
    }
}
