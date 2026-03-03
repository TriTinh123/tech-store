<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SecurityAlert;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Xem trang tài khoản
    public function show()
    {
        $user = auth()->user();

        return view('profile.show', compact('user'));
    }

    // Sửa thông tin cá nhân
    public function edit()
    {
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Thông tin đã cập nhật!');
    }

    // Trang đổi mật khẩu
    public function editPassword()
    {
        return view('profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')->with('success', 'Mật khẩu đã được đổi!');
    }

    // Xem lịch sử đơn hàng
    public function orderHistory()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('profile.order-history', compact('orders'));
    }

    // Xem chi tiết đơn hàng
    public function orderDetail($id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $items = $order->items()->with('product')->get();

        return view('profile.order-detail', compact('order', 'items'));
    }

    /**
     * Show security alerts for current user
     */
    public function alertsIndex(Request $request)
    {
        $user = auth()->user();
        $query = SecurityAlert::where('user_id', $user->id);

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        // Filter by alert type
        if ($request->filled('type')) {
            $query->where('alert_type', $request->type);
        }

        $alerts = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $allAlerts = $user->securityAlerts();
        $stats = [
            'total_count' => $allAlerts->count(),
            'unread_count' => $allAlerts->unread()->count(),
            'critical_count' => $allAlerts->bySeverity('critical')->count(),
            'locked_count' => $allAlerts->where('alert_type', 'account_locked')->count(),
        ];

        return view('profile.alerts', compact('alerts', 'stats'));
    }

    /**
     * Get a specific alert via AJAX
     */
    public function alertsShow($id)
    {
        $alert = SecurityAlert::findOrFail($id);

        // Check if user owns this alert
        if ($alert->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Mark as read
        if (! $alert->isRead()) {
            $alert->markAsRead();
        }

        return response()->json([
            'id' => $alert->id,
            'message' => $alert->message,
            'alertTypeLabel' => $alert->getAlertTypeLabel(),
            'severity' => $alert->severity,
            'created_at' => $alert->created_at->format('H:i:s d/m/Y'),
            'suspiciousLogin' => $alert->suspiciousLogin ? [
                'ip_address' => $alert->suspiciousLogin->ip_address,
                'city' => $alert->suspiciousLogin->city,
                'country' => $alert->suspiciousLogin->country,
                'device_type' => $alert->suspiciousLogin->device_type,
                'browser' => $alert->suspiciousLogin->browser,
            ] : null,
        ]);
    }

    /**
     * Confirm a suspicious login alert
     */
    public function alertsConfirm($id, Request $request)
    {
        $alert = SecurityAlert::findOrFail($id);

        // Check if user owns this alert
        if ($alert->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if there's a suspicious login associated
        if ($alert->suspiciousLogin) {
            $alert->confirmByUser();
        }

        // Redirect back with success message
        return redirect()->route('profile.alerts.index')
            ->with('success', 'Cảnh báo đã được xác nhận!');
    }

    /**
     * Show sessions management page
     */
    public function sessionsIndex()
    {
        $user = auth()->user();
        $sessions = $user->sessions()
            ->orderBy('logged_in_at', 'desc')
            ->paginate(10);

        $stats = [
            'active_sessions' => $user->sessions()->where('status', 'active')->count(),
            'total_sessions' => $user->sessions()->count(),
            'unique_ips' => $user->sessions()->where('status', 'active')->distinct('ip_address')->count(),
        ];

        return view('profile.sessions', compact('sessions', 'stats', 'user'));
    }

    /**
     * Terminate a specific session
     */
    public function sessionTerminate($sessionId)
    {
        $user = auth()->user();
        $session = \App\Models\UserSession::find($sessionId);

        if (! $session || $session->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $session->terminate('user_revoked');

        return redirect()->route('profile.sessions.index')
            ->with('success', 'Phiên đã được kết thúc!');
    }

    /**
     * Terminate all other sessions
     */
    public function sessionsTerminateOthers()
    {
        $user = auth()->user();
        $count = \App\Models\UserSession::where('user_id', $user->id)
            ->where('session_id', '!=', session()->getId())
            ->where('status', 'active')
            ->update([
                'status' => 'terminated',
                'logged_out_at' => now(),
                'logout_reason' => 'user_terminated_others',
            ]);

        return redirect()->route('profile.sessions.index')
            ->with('success', "Đã kết thúc {$count} phiên khác!");
    }
}
