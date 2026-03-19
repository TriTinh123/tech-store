<?php

namespace App\Http\Controllers;

use App\Models\Notification as UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = UserNotification::where('user_id', auth()->id())
            ->latest()->paginate(20);
        $unreadCount = UserNotification::where('user_id', auth()->id())
            ->where('read', false)->count();
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function recent()
    {
        $notifications = UserNotification::where('user_id', auth()->id())
            ->latest()->take(8)->get()
            ->map(fn($n) => [
                'id'           => $n->id,
                'title'        => $n->title,
                'message'      => $n->message,
                'read'         => $n->read,
                'severity'     => $n->severity ?? 'info',
                'action_url'   => $n->action_url,
                'action_label' => $n->action_label,
                'time'         => $n->created_at->diffForHumans(),
            ]);
        $unreadCount = UserNotification::where('user_id', auth()->id())
            ->where('read', false)->count();
        return response()->json(['notifications' => $notifications, 'unreadCount' => $unreadCount]);
    }

    public function markRead($id)
    {
        UserNotification::where('id', $id)->where('user_id', auth()->id())
            ->update(['read' => true, 'read_at' => now()]);
        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        UserNotification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);
        return response()->json(['ok' => true]);
    }
}
