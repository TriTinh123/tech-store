<?php

namespace App\Http\Controllers;

use App\Models\AlertResponse;
use App\Models\SecurityAlert;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AlertManagementAdminController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->notificationService = $notificationService;
    }

    /**
     * Alert management dashboard
     */
    public function dashboard()
    {
        $stats = $this->getStatistics();
        $recentAlerts = $this->getRecentAlerts(10);
        $alertsByType = $this->getAlertsByType();
        $alertsBySeverity = $this->getAlertsBySeverity();
        $alertTrend = $this->getAlertTrend(30);
        $topAffectedUsers = $this->getTopAffectedUsers(5);
        $responseMetrics = $this->getResponseMetrics();
        $pendingAlerts = $this->getPendingAlerts(5);

        return view('admin.alerts.dashboard', [
            'stats' => $stats,
            'recentAlerts' => $recentAlerts,
            'alertsByType' => $alertsByType,
            'alertsBySeverity' => $alertsBySeverity,
            'alertTrend' => $alertTrend,
            'topAffectedUsers' => $topAffectedUsers,
            'responseMetrics' => $responseMetrics,
            'pendingAlerts' => $pendingAlerts,
        ]);
    }

    /**
     * List all alerts with filtering
     */
    public function index(Request $request)
    {
        $query = SecurityAlert::with(['user', 'responses']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('email', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%");
                })
                    ->orWhere('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('alert_type', 'like', "%$search%");
            });
        }

        // Filter by alert type
        if ($request->has('type') && $request->type) {
            $query->where('alert_type', $request->type);
        }

        // Filter by severity
        if ($request->has('severity') && $request->severity) {
            $query->where('severity', $request->severity);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        if (in_array($sortBy, ['created_at', 'severity', 'user_id', 'status'])) {
            $query->orderBy($sortBy, $sortDir);
        }

        $alerts = $query->paginate(50);
        $alertTypes = SecurityAlert::distinct('alert_type')->pluck('alert_type');

        return view('admin.alerts.index', [
            'alerts' => $alerts,
            'alertTypes' => $alertTypes,
        ]);
    }

    /**
     * Show detailed alert with response form
     */
    public function show($id)
    {
        $alert = SecurityAlert::with(['user', 'responses.respondedBy'])->findOrFail($id);
        $responses = $alert->responses()->orderBy('created_at', 'desc')->get();
        $userAlerts = SecurityAlert::where('user_id', $alert->user_id)
            ->where('status', '!=', 'resolved')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.alerts.show', [
            'alert' => $alert,
            'responses' => $responses,
            'userAlerts' => $userAlerts,
        ]);
    }

    /**
     * Alert response center
     */
    public function responseCenter(Request $request)
    {
        $pendingAlerts = SecurityAlert::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $criticalAlerts = SecurityAlert::where('severity', 'critical')
            ->where('status', '!=', 'resolved')
            ->count();

        $responseTemplates = $this->getResponseTemplates();

        return view('admin.alerts.response-center', [
            'pendingAlerts' => $pendingAlerts,
            'criticalAlerts' => $criticalAlerts,
            'responseTemplates' => $responseTemplates,
        ]);
    }

    /**
     * Respond to an alert
     */
    public function respond(Request $request, $id)
    {
        $alert = SecurityAlert::findOrFail($id);

        $request->validate([
            'action' => 'required|in:acknowledge,investigate,escalate,resolve,dismiss',
            'response_notes' => 'nullable|string|max:1000',
            'notify_user' => 'boolean',
            'created_ticket' => 'nullable|string',
        ]);

        $action = $request->action;
        $notes = $request->response_notes ?? '';

        // Record response
        $response = AlertResponse::create([
            'alert_id' => $id,
            'responded_by' => auth()->id(),
            'action' => $action,
            'notes' => $notes,
            'timestamp' => now(),
        ]);

        // Update alert status based on action
        if ($action === 'acknowledge') {
            $alert->status = 'acknowledged';
        } elseif ($action === 'investigate') {
            $alert->status = 'under_investigation';
        } elseif ($action === 'escalate') {
            $alert->status = 'escalated';
            $this->escalateAlert($alert, $notes);
        } elseif ($action === 'resolve') {
            $alert->status = 'resolved';
            $alert->resolved_at = now();
            $alert->resolved_by = auth()->id();
        } elseif ($action === 'dismiss') {
            $alert->status = 'dismissed';
        }

        $alert->last_response_at = now();
        $alert->last_responded_by = auth()->id();
        $alert->save();

        // Notify user if requested
        if ($request->has('notify_user') && $request->notify_user) {
            $this->notifyUserAboutResponse($alert, $action, $notes);
        }

        return back()->with('success', 'Alert responded with action: '.ucfirst($action));
    }

    /**
     * Acknowledge alert
     */
    public function acknowledge($id)
    {
        $alert = SecurityAlert::findOrFail($id);
        $alert->status = 'acknowledged';
        $alert->acknowledged_at = now();
        $alert->acknowledged_by = auth()->id();
        $alert->save();

        AlertResponse::create([
            'alert_id' => $id,
            'responded_by' => auth()->id(),
            'action' => 'acknowledge',
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Alert acknowledged');
    }

    /**
     * Investigate alert
     */
    public function investigate($id)
    {
        $alert = SecurityAlert::findOrFail($id);
        $alert->status = 'under_investigation';
        $alert->save();

        AlertResponse::create([
            'alert_id' => $id,
            'responded_by' => auth()->id(),
            'action' => 'investigate',
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Alert marked for investigation');
    }

    /**
     * Resolve alert
     */
    public function resolve(Request $request, $id)
    {
        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $alert = SecurityAlert::findOrFail($id);
        $alert->status = 'resolved';
        $alert->resolved_at = now();
        $alert->resolved_by = auth()->id();
        $alert->save();

        AlertResponse::create([
            'alert_id' => $id,
            'responded_by' => auth()->id(),
            'action' => 'resolve',
            'notes' => $request->resolution_notes,
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Alert resolved successfully');
    }

    /**
     * Dismiss alert
     */
    public function dismiss(Request $request, $id)
    {
        $request->validate([
            'dismiss_reason' => 'required|string',
        ]);

        $alert = SecurityAlert::findOrFail($id);
        $alert->status = 'dismissed';
        $alert->save();

        AlertResponse::create([
            'alert_id' => $id,
            'responded_by' => auth()->id(),
            'action' => 'dismiss',
            'notes' => $request->dismiss_reason,
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Alert dismissed');
    }

    /**
     * Bulk actions on alerts
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'alert_ids' => 'required|array',
            'action' => 'required|in:acknowledge,investigate,resolve,dismiss',
        ]);

        $alertIds = $request->alert_ids;
        $action = $request->action;
        $count = 0;

        foreach ($alertIds as $alertId) {
            $alert = SecurityAlert::find($alertId);
            if (! $alert) {
                continue;
            }

            $alert->status = $action === 'acknowledge' ? 'acknowledged' :
                           ($action === 'investigate' ? 'under_investigation' :
                           ($action === 'resolve' ? 'resolved' : 'dismissed'));
            $alert->save();

            AlertResponse::create([
                'alert_id' => $alertId,
                'responded_by' => auth()->id(),
                'action' => $action,
                'timestamp' => now(),
            ]);

            $count++;
        }

        return back()->with('success', "Applied {$action} action to {$count} alerts");
    }

    /**
     * Export alerts
     */
    public function export(Request $request)
    {
        $query = SecurityAlert::with('user');

        if ($request->has('severity') && $request->severity) {
            $query->where('severity', $request->severity);
        }
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $format = $request->get('format', 'csv');
        $alerts = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'json') {
            return response()->json($alerts, 200, [
                'Content-Disposition' => 'attachment; filename="alerts-'.now()->format('Y-m-d').'.json"',
            ]);
        }

        // CSV Export
        $filename = 'alerts-'.now()->format('Y-m-d-His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $rows = [];
        foreach ($alerts as $alert) {
            $rows[] = [
                $alert->id,
                $alert->user->email ?? 'N/A',
                $alert->alert_type,
                $alert->title,
                $alert->severity,
                $alert->status,
                $alert->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return response()->stream(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'User Email', 'Type', 'Title', 'Severity', 'Status', 'Created']);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Alert statistics report
     */
    public function statistics()
    {
        $report = [
            'total_alerts' => SecurityAlert::count(),
            'by_severity' => $this->getAlertsBySeverity(),
            'by_status' => SecurityAlert::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'by_type' => SecurityAlert::selectRaw('alert_type, COUNT(*) as count')
                ->groupBy('alert_type')
                ->orderBy('count', 'desc')
                ->pluck('count', 'alert_type')
                ->toArray(),
            'response_time_avg' => $this->getAverageResponseTime(),
            'resolution_rate' => $this->getResolutionRate(),
            'critical_alerts_this_month' => SecurityAlert::where('severity', 'critical')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
            'unresolved_count' => SecurityAlert::whereIn('status', ['pending', 'acknowledged', 'under_investigation', 'escalated'])
                ->count(),
        ];

        return view('admin.alerts.statistics', ['report' => $report]);
    }

    /**
     * ==================== HELPER METHODS ====================
     */
    protected function getStatistics()
    {
        return [
            'today' => SecurityAlert::where('created_at', '>=', now()->startOfDay())->count(),
            'this_month' => SecurityAlert::where('created_at', '>=', now()->startOfMonth())->count(),
            'critical_today' => SecurityAlert::where('severity', 'critical')
                ->where('created_at', '>=', now()->startOfDay())->count(),
            'pending' => SecurityAlert::where('status', 'pending')->count(),
            'acknowledged' => SecurityAlert::where('status', 'acknowledged')->count(),
            'resolved' => SecurityAlert::where('status', 'resolved')->count(),
            'unresolved' => SecurityAlert::whereIn('status', ['pending', 'acknowledged', 'under_investigation', 'escalated'])->count(),
        ];
    }

    protected function getRecentAlerts($limit = 10)
    {
        return SecurityAlert::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function getAlertsByType()
    {
        return SecurityAlert::selectRaw('alert_type, COUNT(*) as count')
            ->groupBy('alert_type')
            ->orderBy('count', 'desc')
            ->get();
    }

    protected function getAlertsBySeverity()
    {
        return [
            'critical' => SecurityAlert::where('severity', 'critical')->count(),
            'high' => SecurityAlert::where('severity', 'high')->count(),
            'medium' => SecurityAlert::where('severity', 'medium')->count(),
            'low' => SecurityAlert::where('severity', 'low')->count(),
        ];
    }

    protected function getAlertTrend($days = 30)
    {
        $data = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = SecurityAlert::whereDate('created_at', $date->format('Y-m-d'))->count();
            $data[] = [
                'date' => $date->format('M d'),
                'count' => $count,
            ];
        }

        return $data;
    }

    protected function getTopAffectedUsers($limit = 5)
    {
        return SecurityAlert::selectRaw('user_id, COUNT(*) as alert_count, MAX(severity) as highest_severity')
            ->groupBy('user_id')
            ->orderBy('alert_count', 'desc')
            ->limit($limit)
            ->with('user')
            ->get();
    }

    protected function getResponseMetrics()
    {
        $totalAlerts = SecurityAlert::count();
        $respondedAlerts = SecurityAlert::whereNotNull('last_response_at')->count();

        return [
            'response_rate' => $totalAlerts > 0 ? round(($respondedAlerts / $totalAlerts) * 100, 1) : 0,
            'avg_response_time' => $this->getAverageResponseTime(),
            'total_responses' => AlertResponse::count(),
        ];
    }

    protected function getPendingAlerts($limit = 5)
    {
        return SecurityAlert::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    protected function escalateAlert($alert, $reason)
    {
        // Send escalation notification to management
        // This would integrate with your notification service
    }

    protected function notifyUserAboutResponse($alert, $action, $notes)
    {
        // Send notification to user about the response
        // This would integrate with a notification service if available
        // Example: $this->notificationService->notify($alert->user, $alert, $action, $notes);

        if ($alert->user) {
            // Logging for notification tracking
            \Log::info('Alert response notification sent', [
                'user_id' => $alert->user->id,
                'alert_id' => $alert->id,
                'action' => $action,
            ]);
        }
    }

    protected function getResponseTemplates()
    {
        return [
            [
                'title' => 'Acknowledged',
                'action' => 'acknowledge',
                'template' => 'Alert has been acknowledged. Will investigate shortly.',
            ],
            [
                'title' => 'Under Investigation',
                'action' => 'investigate',
                'template' => 'We are currently investigating this alert. You will be notified of updates.',
            ],
            [
                'title' => 'False Positive',
                'action' => 'resolve',
                'template' => 'This alert was identified as a false positive and has been resolved.',
            ],
            [
                'title' => 'User Confirmed Activity',
                'action' => 'dismiss',
                'template' => 'User confirmed this is legitimate activity. Alert dismissed.',
            ],
        ];
    }

    protected function getAverageResponseTime()
    {
        $alerts = SecurityAlert::whereNotNull('last_response_at')
            ->selectRaw('TIMEDIFF(last_response_at, created_at) as response_time')
            ->limit(100)
            ->get();

        if ($alerts->count() === 0) {
            return 0;
        }

        $totalSeconds = 0;
        foreach ($alerts as $alert) {
            // Parse time difference
            $totalSeconds += strlen($alert->response_time) * 60; // rough estimate
        }

        return round($totalSeconds / $alerts->count() / 60, 1); // Return as minutes
    }

    protected function getResolutionRate()
    {
        $totalAlerts = SecurityAlert::count();
        $resolvedAlerts = SecurityAlert::where('status', 'resolved')->count();

        return $totalAlerts > 0 ? round(($resolvedAlerts / $totalAlerts) * 100, 1) : 0;
    }
}
