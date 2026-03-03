<?php

namespace App\Events;

use App\Models\ReturnRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReturnStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(public ReturnRequest $return, public string $newStatus) {}
}
