<?php

namespace App\Listeners;

use App\Models\LoginLog;
use Illuminate\Auth\Events\Login;

class RecordLoginAttempt
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        if ($event->user) {
            $userId = method_exists($event->user, 'getAuthIdentifier')
                ? $event->user->getAuthIdentifier()
                : ($event->user->id ?? null);

            if ($userId) {
                LoginLog::create([
                    'user_id' => $userId,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                    'login_at' => now(),
                ]);
            }
        }
    }
}
