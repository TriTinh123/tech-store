<?php

namespace App\Listeners;

use App\Events\OrderShipped;
use App\Mail\ShippingUpdateMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendShippingUpdateEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderShipped $event): void
    {
        Mail::to($event->order->user->email)->send(
            new ShippingUpdateMail($event->order)
        );
    }
}
