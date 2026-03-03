<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Display user's notifications
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', auth()->id());

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->where('read', true);
            }
        }

        // Filter by severity
        if ($request->filled('severity')) {
            $query->bySeverity($request->severity);
        }

        $notifications = $query->orderByDesc('created_at')->paginate(15);

        $stats = $this->notificationService->getStatistics(auth()->user());

        return view('notifications.index', [
            'notifications' => $notifications,
            'stats' => $stats,
            'filterType' => $request->type,
            'filterStatus' => $request->status,
            'filterSeverity' => $request->severity,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsUnread();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->unread()
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Tất cả thông báo đã được đánh dấu là đã đọc');
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Thông báo đã được xóa');
    }

    /**
     * Display notification preferences
     */
    public function preferences()
    {
        $user = auth()->user();
        $preferences = $user->notificationPreferences ??
            NotificationPreference::firstOrCreate(['user_id' => $user->id]);

        return view('notifications.preferences', [
            'preferences' => $preferences,
        ]);
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'in_app_enabled' => 'boolean',
            'notify_concurrent_login' => 'boolean',
            'notify_suspicious_activity' => 'boolean',
            'notify_3fa_changes' => 'boolean',
            'notify_ip_blocked' => 'boolean',
            'notify_password_change' => 'boolean',
            'notify_new_device' => 'boolean',
            'notify_location_change' => 'boolean',
            'email_frequency' => 'in:immediate,daily,weekly',
            'sms_frequency' => 'in:immediate,daily,weekly',
            'quiet_hours_enabled' => 'boolean',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
        ]);

        $preferences = auth()->user()->notificationPreferences ??
            NotificationPreference::firstOrCreate(['user_id' => auth()->id()]);

        $preferences->update($validated);

        return back()->with('success', 'Tùy chọn thông báo đã được cập nhật');
    }

    /**
     * Update email preferences
     */
    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'notification_email' => 'required|email',
        ]);

        $preferences = auth()->user()->notificationPreferences ??
            NotificationPreference::firstOrCreate(['user_id' => auth()->id()]);

        $preferences->enableEmail($validated['notification_email']);

        return back()->with('success', 'Email thông báo đã được cập nhật');
    }

    /**
     * Setup SMS notifications
     */
    public function setupSms(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'required|regex:/^\+?1?\d{9,15}$/',
        ]);

        // In production, use Twilio verify API to send OTP
        $preferences = auth()->user()->notificationPreferences ??
            NotificationPreference::firstOrCreate(['user_id' => auth()->id()]);

        // For now, just enable it directly
        $preferences->enableSms($validated['phone_number']);

        return back()->with('success', 'Số điện thoại đã được lưu. Vui lòng xác minh OTP');
    }

    /**
     * Verify SMS phone number
     */
    public function verifySmsOtp(Request $request)
    {
        $validated = $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $preferences = auth()->user()->notificationPreferences;

        if (! $preferences || ! $preferences->phone_number) {
            return back()->withErrors(['otp' => 'Không có số điện thoại để xác minh']);
        }

        // In production, verify against Twilio Verify API
        // For now, assume OTP is valid if provided
        $preferences->update([
            'phone_verified' => true,
            'phone_verified_at' => now(),
        ]);

        return back()->with('success', 'Số điện thoại đã được xác minh');
    }

    /**
     * Get unread notification count (AJAX)
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', auth()->id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (AJAX)
     */
    public function getRecent(Request $request)
    {
        $limit = $request->input('limit', 5);

        $notifications = Notification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'severity' => $notification->severity,
                    'icon' => $this->getSeverityIcon($notification),
                    'color' => $this->getSeverityColor($notification),
                    'read' => $notification->read,
                    'action_url' => $notification->action_url,
                    'action_label' => $notification->action_label,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json($notifications);
    }
}
