<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display main audit trail dashboard
     */
    public function dashboard()
    {
        $data = [
            'statistics' => $this->getStatistics(),
            'recentLogs' => $this->getRecentLogs(20),
            'topActions' => $this->getTopActions(10),
            'topUsers' => $this->getTopUsers(10),
            'securityEvents' => $this->getSecurityEvents(10),
            'failedAttempts' => $this->getFailedAttempts(10),
            'activityTrend' => $this->getActivityTrend(),
            'riskLevels' => $this->getRiskLevelBreakdown(),
        ];

        return view('audit.dashboard', $data);
    }

    /**
     * Display audit logs with search and filter
     */
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        // Search by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search by action
        if ($request->filled('action')) {
            $query->where('action', 'like', '%'.$request->action.'%');
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%'.$request->search.'%')
                    ->orWhere('action', 'like', '%'.$request->search.'%')
                    ->orWhere('ip_address', 'like', '%'.$request->search.'%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter suspicious activity
        if ($request->filled('suspicious') && $request->suspicious === 'true') {
            $query->where('is_suspicious', true);
        }

        // Filter by threat type
        if ($request->filled('threat_type')) {
            $query->where('threat_type', $request->threat_type);
        }

        // Sort options
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        if (in_array($sortBy, ['created_at', 'user_id', 'action', 'status', 'ip_address'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $logs = $query->with('user')
            ->paginate($request->get('per_page', 50))
            ->appends($request->query());

        $users = User::select('id', 'name', 'email')->get();
        $actions = ActivityLog::distinct()->pluck('action');
        $statuses = ['success', 'failed', 'blocked', 'suspicious'];
        $threatTypes = ActivityLog::distinct()->pluck('threat_type');

        return view('audit.index', compact('logs', 'users', 'actions', 'statuses', 'threatTypes'));
    }

    /**
     * Show detailed audit log entry
     */
    public function show($id)
    {
        $log = ActivityLog::with(['user'])->findOrFail($id);

        // Parse additional data if available
        $additionalData = [];
        if ($log->data) {
            $additionalData = json_decode($log->data, true) ?? [];
        }

        // Get related logs (same user, same day)
        $relatedLogs = ActivityLog::where('user_id', $log->user_id)
            ->whereDate('created_at', $log->created_at->toDateString())
            ->where('id', '!=', $log->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Get timeline of similar actions
        $timeline = ActivityLog::where('user_id', $log->user_id)
            ->where('action', $log->action)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('audit.show', compact('log', 'additionalData', 'relatedLogs', 'timeline'));
    }

    /**
     * Search audit logs
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
            'search_type' => 'in:user,action,ip,description',
        ]);

        $query = ActivityLog::query();
        $searchType = $validated['search_type'] ?? 'description';
        $searchQuery = $validated['query'];

        switch ($searchType) {
            case 'user':
                $query->whereHas('user', function ($q) use ($searchQuery) {
                    $q->where('name', 'like', '%'.$searchQuery.'%')
                        ->orWhere('email', 'like', '%'.$searchQuery.'%');
                });
                break;
            case 'action':
                $query->where('action', 'like', '%'.$searchQuery.'%');
                break;
            case 'ip':
                $query->where('ip_address', 'like', '%'.$searchQuery.'%');
                break;
            default:
                $query->where('description', 'like', '%'.$searchQuery.'%');
        }

        $logs = $query->with('user')
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('audit.search', compact('logs', 'searchQuery', 'searchType'));
    }

    /**
     * Advanced filter view
     */
    public function filter(Request $request)
    {
        $query = ActivityLog::query();

        // Build complex filter
        if ($request->filled('filters')) {
            $filters = $request->get('filters', []);

            foreach ($filters as $filter) {
                if (isset($filter['field']) && isset($filter['operator']) && isset($filter['value'])) {
                    $this->applyFilter($query, $filter['field'], $filter['operator'], $filter['value']);
                }
            }
        }

        $logs = $query->with('user')
            ->orderByDesc('created_at')
            ->paginate(50);

        $availableFields = [
            'user_id' => 'User',
            'action' => 'Action',
            'status' => 'Status',
            'ip_address' => 'IP Address',
            'threat_type' => 'Threat Type',
            'is_suspicious' => 'Suspicious Activity',
        ];

        return view('audit.filter', compact('logs', 'availableFields'));
    }

    /**
     * Apply filter condition
     */
    private function applyFilter($query, $field, $operator, $value)
    {
        switch ($operator) {
            case 'equals':
                $query->where($field, '=', $value);
                break;
            case 'not_equals':
                $query->where($field, '!=', $value);
                break;
            case 'contains':
                $query->where($field, 'like', '%'.$value.'%');
                break;
            case 'not_contains':
                $query->where($field, 'not like', '%'.$value.'%');
                break;
            case 'starts_with':
                $query->where($field, 'like', $value.'%');
                break;
            case 'ends_with':
                $query->where($field, 'like', '%'.$value);
                break;
            case 'greater_than':
                $query->where($field, '>', $value);
                break;
            case 'less_than':
                $query->where($field, '<', $value);
                break;
        }
    }

    /**
     * Get user activity logs
     */
    public function userActivity($userId)
    {
        $user = User::findOrFail($userId);
        $logs = ActivityLog::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(100);

        $userStats = [
            'totalActions' => ActivityLog::where('user_id', $userId)->count(),
            'successfulActions' => ActivityLog::where('user_id', $userId)->where('status', 'success')->count(),
            'failedActions' => ActivityLog::where('user_id', $userId)->where('status', 'failed')->count(),
            'suspiciousActions' => ActivityLog::where('user_id', $userId)->where('is_suspicious', true)->count(),
            'lastActivity' => ActivityLog::where('user_id', $userId)->orderByDesc('created_at')->first(),
        ];

        return view('audit.user-activity', compact('user', 'logs', 'userStats'));
    }

    /**
     * Get activity by time period
     */
    public function timePeriod(Request $request)
    {
        $period = $request->get('period', 'today'); // today, week, month, year
        $dateFrom = Carbon::now();

        switch ($period) {
            case 'today':
                $dateFrom = Carbon::today();
                break;
            case 'week':
                $dateFrom = Carbon::now()->startOfWeek();
                break;
            case 'month':
                $dateFrom = Carbon::now()->startOfMonth();
                break;
            case 'year':
                $dateFrom = Carbon::now()->startOfYear();
                break;
        }

        $logs = ActivityLog::where('created_at', '>=', $dateFrom)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(50);

        $statistics = [
            'total' => $logs->total(),
            'successful' => ActivityLog::where('created_at', '>=', $dateFrom)->where('status', 'success')->count(),
            'failed' => ActivityLog::where('created_at', '>=', $dateFrom)->where('status', 'failed')->count(),
            'suspicious' => ActivityLog::where('created_at', '>=', $dateFrom)->where('is_suspicious', true)->count(),
        ];

        return view('audit.time-period', compact('logs', 'period', 'statistics'));
    }

    /**
     * Export logs as CSV
     */
    public function exportCsv(Request $request)
    {
        $query = $this->buildQueryFromFilters($request);
        $logs = $query->orderByDesc('created_at')->get();

        $csv = "ID,User,Email,Action,Description,Status,IP Address,Country,Threat Type,Suspicious,Date/Time\n";

        foreach ($logs as $log) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s"%s',
                $log->id,
                $log->user?->name ?? 'N/A',
                $log->user?->email ?? 'N/A',
                $log->action,
                $log->description,
                $log->status,
                $log->ip_address,
                $log->country ?? 'N/A',
                $log->threat_type ?? 'N/A',
                $log->is_suspicious ? 'Yes' : 'No',
                $log->created_at->format('Y-m-d H:i:s'),
                "\n"
            );
        }

        return Response::download(
            tap(tmpfile(), function ($resource) use ($csv) {
                fwrite($resource, $csv);
            }),
            'audit-logs-'.now()->format('Y-m-d-His').'.csv',
            ['Content-Type' => 'text/csv']
        )->deleteFileAfterSend(true);
    }

    /**
     * Export logs as JSON
     */
    public function exportJson(Request $request)
    {
        $query = $this->buildQueryFromFilters($request);
        $logs = $query->orderByDesc('created_at')->get();

        $data = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'user' => $log->user?->name,
                'email' => $log->user?->email,
                'action' => $log->action,
                'description' => $log->description,
                'status' => $log->status,
                'ip_address' => $log->ip_address,
                'country' => $log->country,
                'threat_type' => $log->threat_type,
                'is_suspicious' => $log->is_suspicious,
                'timestamp' => $log->created_at,
            ];
        });

        return Response::json([
            'exported_at' => now(),
            'total_records' => $data->count(),
            'logs' => $data,
        ])->download('audit-logs-'.now()->format('Y-m-d-His').'.json');
    }

    /**
     * Build query from filter parameters
     */
    private function buildQueryFromFilters(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('suspicious') && $request->suspicious === 'true') {
            $query->where('is_suspicious', true);
        }

        return $query;
    }

    /**
     * Get statistics
     */
    private function getStatistics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'totalLogs' => ActivityLog::count(),
            'logsToday' => ActivityLog::whereDate('created_at', $today)->count(),
            'logsThisMonth' => ActivityLog::where('created_at', '>=', $thisMonth)->count(),
            'suspiciousToday' => ActivityLog::whereDate('created_at', $today)->where('is_suspicious', true)->count(),
            'failedAttemptsToday' => ActivityLog::whereDate('created_at', $today)->where('status', 'failed')->count(),
            'uniqueUsersToday' => ActivityLog::whereDate('created_at', $today)->distinct()->count('user_id'),
            'uniqueIpsToday' => ActivityLog::whereDate('created_at', $today)->distinct()->count('ip_address'),
        ];
    }

    /**
     * Get recent logs
     */
    private function getRecentLogs($limit = 20)
    {
        return ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($log) => [
                'id' => $log->id,
                'user' => $log->user?->name,
                'action' => $log->action,
                'description' => $log->description,
                'status' => $log->status,
                'ip_address' => $log->ip_address,
                'is_suspicious' => $log->is_suspicious,
                'time' => $log->created_at->diffForHumans(),
            ]);
    }

    /**
     * Get top actions
     */
    private function getTopActions($limit = 10)
    {
        return ActivityLog::selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top users
     */
    private function getTopUsers($limit = 10)
    {
        return ActivityLog::selectRaw('user_id, COUNT(*) as count')
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get security events
     */
    private function getSecurityEvents($limit = 10)
    {
        return ActivityLog::where('is_suspicious', true)
            ->orWhere('status', 'failed')
            ->with('user')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get failed attempts
     */
    private function getFailedAttempts($limit = 10)
    {
        return ActivityLog::where('status', 'failed')
            ->with('user')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity trend (last 30 days)
     */
    private function getActivityTrend()
    {
        $trend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = ActivityLog::whereDate('created_at', $date)->count();
            $trend[$date->format('Y-m-d')] = $count;
        }

        return $trend;
    }

    /**
     * Get risk level breakdown
     */
    private function getRiskLevelBreakdown()
    {
        return [
            'critical' => ActivityLog::where('threat_type', 'critical')->count(),
            'high' => ActivityLog::where('threat_type', 'high')->count(),
            'medium' => ActivityLog::where('threat_type', 'medium')->count(),
            'low' => ActivityLog::where('threat_type', 'low')->count(),
        ];
    }

    /**
     * Generate compliance report
     */
    public function complianceReport(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'include_failed' => 'boolean',
            'include_suspicious' => 'boolean',
        ]);

        $query = ActivityLog::whereBetween('created_at', [$validated['date_from'], $validated['date_to']]);

        if ($validated['include_failed'] ?? false) {
            $query->orWhere('status', 'failed');
        }
        if ($validated['include_suspicious'] ?? false) {
            $query->orWhere('is_suspicious', true);
        }

        $report = [
            'period' => [
                'from' => $validated['date_from'],
                'to' => $validated['date_to'],
            ],
            'summary' => [
                'total_events' => $query->count(),
                'unique_users' => $query->distinct()->count('user_id'),
                'unique_ips' => $query->distinct()->count('ip_address'),
                'failed_attempts' => ActivityLog::whereBetween('created_at', [$validated['date_from'], $validated['date_to']])
                    ->where('status', 'failed')->count(),
                'suspicious_events' => ActivityLog::whereBetween('created_at', [$validated['date_from'], $validated['date_to']])
                    ->where('is_suspicious', true)->count(),
            ],
            'by_action' => ActivityLog::whereBetween('created_at', [$validated['date_from'], $validated['date_to']])
                ->selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->get(),
            'by_user' => ActivityLog::whereBetween('created_at', [$validated['date_from'], $validated['date_to']])
                ->selectRaw('user_id, COUNT(*) as count')
                ->with('user:id,name,email')
                ->groupBy('user_id')
                ->get(),
        ];

        return view('audit.compliance-report', compact('report'));
    }
}
