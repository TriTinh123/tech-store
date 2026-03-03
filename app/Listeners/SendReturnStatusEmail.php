<?php

namespace App\Listeners;

use App\Events\ReturnStatusChanged;
use App\Mail\ReturnStatusMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendReturnStatusEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ReturnStatusChanged $event): void
    {
        Mail::to($event->return->order->user->email)->send(
            new ReturnStatusMail($event->return, $event->newStatus)
        );
    }
}
