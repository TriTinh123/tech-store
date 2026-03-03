<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\SystemLog;

class LogController extends Controller
{
    /**
     * Display login logs
     */
    public function loginLogs()
    {
        $logs = LoginLog::with('user')
            ->orderBy('login_at', 'desc')
            ->paginate(15);

        return view('admin.logs.login', compact('logs'));
    }

    /**
     * Display system logs
     */
    public function systemLogs()
    {
        $logs = SystemLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.logs.system', compact('logs'));
    }
}
