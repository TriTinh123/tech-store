<?php

namespace App\Http\Controllers;

use App\Models\BlockedIp;
use App\Models\LoginAnomaly;
use App\Models\User;
use App\Services\AnomalyDetectionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnomalyDetectionAdminController extends Controller
{
    protected $anomalyService;

    public function __construct(AnomalyDetectionService $anomalyService)
    {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->anomalyService = $anomalyService;
    }

    /**
     * Dashboard with overview and statistics
     */
    public function dashboard()
    {
        $stats = $this->getStatistics();
        $recentAnomalies = $this->getRecentAnomalies(10);
        $topTargetedUsers = $this->getTopTargetedUsers(5);
        $riskLevelBreakdown = $this->getRiskLevelBreakdown();
        $anomalyTrend = $this->getAnomalyTrend(30);
        $topAnomalyTypes = $this->getTopAnomalyTypes(8);
        $geoAnomalies = $this->getGeoAnomalies(5);
        $failedLoginAttempts = $this->getFailedLoginAttempts(5);

        return view('admin.anomalies.dashboard', [
            'stats' => $stats,
            'recentAnomalies' => $recentAnomalies,
            'topTargetedUsers' => $topTargetedUsers,
            'riskLevelBreakdown' => $riskLevelBreakdown,
            'anomalyTrend' => $anomalyTrend,
            'topAnomalyTypes' => $topAnomalyTypes,
            'geoAnomalies' => $geoAnomalies,
            'failedLoginAttempts' => $failedLoginAttempts,
        ]);
    }

    /**
     * List all anomalies with filtering
     */
    public function index(Request $request)
    {
        $query = LoginAnomaly::with(['user', 'securityEvent']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('email', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%");
                })
                    ->orWhere('ip_address', 'like', "%$search%")
                    ->orWhere('anomaly_type', 'like', "%$search%");
            });
        }

        // Filter by anomaly type
        if ($request->has('type') && $request->type) {
            $query->where('anomaly_type', $request->type);
        }

        // Filter by risk level
        if ($request->has('risk_level') && $request->risk_level) {
            $query->where('risk_level', $request->risk_level);
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
        if (in_array($sortBy, ['created_at', 'risk_level', 'user_id', 'ip_address'])) {
            $query->orderBy($sortBy, $sortDir);
        }

        $anomalies = $query->paginate(50);
        $anomalyTypes = LoginAnomaly::distinct('anomaly_type')->pluck('anomaly_type');

        return view('admin.anomalies.index', [
            'anomalies' => $anomalies,
            'anomalyTypes' => $anomalyTypes,
            'users' => User::pluck('name', 'id'),
        ]);
    }

    /**
     * Show detailed anomaly information
     */
    public function show($id)
    {
        $anomaly = LoginAnomaly::with(['user', 'securityEvent'])->findOrFail($id);

        $userAnomalies = LoginAnomaly::where('user_id', $anomaly->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $similarAnomalies = LoginAnomaly::where('ip_address', $anomaly->ip_address)
            ->where('id', '!=', $anomaly->id)
            ->limit(10)
            ->get();

        $userHistory = $this->getUserAnomalyHistory($anomaly->user_id, 20);

        return view('admin.anomalies.show', [
            'anomaly' => $anomaly,
            'userAnomalies' => $userAnomalies,
            'similarAnomalies' => $similarAnomalies,
            'userHistory' => $userHistory,
        ]);
    }

    /**
     * List anomalies by specific user
     */
    public function byUser($userId)
    {
        $user = User::findOrFail($userId);
        $anomalies = LoginAnomaly::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->with('securityEvent')
            ->paginate(50);

        $stats = [
            'total' => LoginAnomaly::where('user_id', $userId)->count(),
            'critical' => LoginAnomaly::where('user_id', $userId)->where('risk_level', 'critical')->count(),
            'high' => LoginAnomaly::where('user_id', $userId)->where('risk_level', 'high')->count(),
            'medium' => LoginAnomaly::where('user_id', $userId)->where('risk_level', 'medium')->count(),
            'low' => LoginAnomaly::where('user_id', $userId)->where('risk_level', 'low')->count(),
        ];

        return view('admin.anomalies.by-user', [
            'user' => $user,
            'anomalies' => $anomalies,
            'stats' => $stats,
        ]);
    }

    /**
     * Geographic anomalies view
     */
    public function geoMap()
    {
        $geoAnomalies = LoginAnomaly::where('country', '!=', null)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('country')
            ->selectRaw('country, COUNT(*) as count, MAX(risk_level) as max_risk')
            ->orderBy('count', 'desc')
            ->get();

        $countryRisks = $geoAnomalies->map(function ($item) {
            return [
                'country' => $item->country,
                'count' => $item->count,
                'risk' => $item->max_risk,
                'color' => $this->getRiskColor($item->max_risk),
            ];
        });

        $topCities = LoginAnomaly::where('city', '!=', null)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(['city', 'country'])
            ->selectRaw('city, country, COUNT(*) as count, MAX(risk_level) as max_risk')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();

        return view('admin.anomalies.geo-map', [
            'geoAnomalies' => $geoAnomalies,
            'countryRisks' => $countryRisks,
            'topCities' => $topCities,
        ]);
    }

    /**
     * Device anomalies
     */
    public function devices()
    {
        $deviceAnomalies = LoginAnomaly::where('anomaly_type', 'new_device')
            ->where('created_at', '>=', now()->subDays(30))
            ->with('user')
            ->paginate(50);

        $stats = [
            'total_device_anomalies' => LoginAnomaly::where('anomaly_type', 'new_device')->count(),
            'today' => LoginAnomaly::where('anomaly_type', 'new_device')
                ->where('created_at', '>=', now()->startOfDay())
                ->count(),
            'this_month' => LoginAnomaly::where('anomaly_type', 'new_device')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
        ];

        return view('admin.anomalies.devices', [
            'anomalies' => $deviceAnomalies,
            'stats' => $stats,
        ]);
    }

    /**
     * IP address anomalies
     */
    public function ips()
    {
        $query = LoginAnomaly::selectRaw('ip_address, COUNT(*) as count, MAX(risk_level) as max_risk, MAX(created_at) as last_seen')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('ip_address')
            ->orderBy('count', 'desc');

        $ips = $query->paginate(50);

        $stats = [
            'blocked_ips' => BlockedIp::where('blocked_at', '>=', now()->subDays(30))->count(),
            'suspicious_ips' => LoginAnomaly::distinct('ip_address')
                ->where('risk_level', '>=', 'high')
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
        ];

        return view('admin.anomalies.ips', [
            'ips' => $ips,
            'stats' => $stats,
        ]);
    }

    /**
     * Resolve/investigate anomaly
     */
    public function resolve(Request $request, $id)
    {
        $anomaly = LoginAnomaly::findOrFail($id);

        $request->validate([
            'action' => 'required|in:investigate,whitelist,block,resolve',
            'notes' => 'nullable|string',
        ]);

        $action = $request->action;
        $notes = $request->notes ?? '';

        if ($action === 'whitelist') {
            $anomaly->is_whitelisted = true;
            $anomaly->whitelisted_at = now();
            $anomaly->whitelisted_by = auth()->id();
        } elseif ($action === 'block') {
            // Block the IP (will be handled by blockIpSessions)
            $this->blockIpSessions($anomaly->ip_address);
            $anomaly->status = 'blocked';
        } elseif ($action === 'investigate') {
            $anomaly->status = 'under_investigation';
        } elseif ($action === 'resolve') {
            $anomaly->status = 'resolved';
        }

        $anomaly->admin_notes = $notes;
        $anomaly->handled_by = auth()->id();
        $anomaly->handled_at = now();
        $anomaly->save();

        return back()->with('success', "Anomaly marked as {$action}");
    }

    /**
     * Block IP address
     */
    public function blockIp(Request $request, $ipAddress)
    {
        $request->validate([
            'reason' => 'required|string',
            'duration_hours' => 'required|integer|min:1',
        ]);

        BlockedIp::create([
            'ip_address' => $ipAddress,
            'reason' => $request->reason,
            'blocked_at' => now(),
            'unblock_at' => now()->addHours($request->duration_hours),
            'blocked_by_admin_id' => auth()->id(),
        ]);

        // Lock out all active sessions from this IP
        $this->lockoutIpSessions($ipAddress);

        return back()->with('success', "IP {$ipAddress} blocked for {$request->duration_hours} hours");
    }

    /**
     * Export anomalies
     */
    public function export(Request $request)
    {
        $query = LoginAnomaly::with('user');

        if ($request->has('type') && $request->type) {
            $query->where('anomaly_type', $request->type);
        }

        if ($request->has('risk_level') && $request->risk_level) {
            $query->where('risk_level', $request->risk_level);
        }

        $format = $request->get('format', 'csv');
        $anomalies = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'json') {
            return response()->json($anomalies, 200, [
                'Content-Disposition' => 'attachment; filename="anomalies-'.now()->format('Y-m-d').'.json"',
            ]);
        }

        // CSV Export
        $filename = 'anomalies-'.now()->format('Y-m-d-His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $rows = [];
        foreach ($anomalies as $anomaly) {
            $rows[] = [
                $anomaly->id,
                $anomaly->user->email ?? 'N/A',
                $anomaly->anomaly_type,
                $anomaly->ip_address,
                $anomaly->country ?? 'N/A',
                $anomaly->risk_level,
                $anomaly->status,
                $anomaly->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return response()->stream(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'User Email', 'Anomaly Type', 'IP Address', 'Country', 'Risk Level', 'Status', 'Timestamp']);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Generate risk report
     */
    public function riskReport()
    {
        $report = [
            'period' => 'Last 30 Days',
            'total_anomalies' => LoginAnomaly::where('created_at', '>=', now()->subDays(30))->count(),
            'by_risk_level' => [
                'critical' => LoginAnomaly::where('risk_level', 'critical')->where('created_at', '>=', now()->subDays(30))->count(),
                'high' => LoginAnomaly::where('risk_level', 'high')->where('created_at', '>=', now()->subDays(30))->count(),
                'medium' => LoginAnomaly::where('risk_level', 'medium')->where('created_at', '>=', now()->subDays(30))->count(),
                'low' => LoginAnomaly::where('risk_level', 'low')->where('created_at', '>=', now()->subDays(30))->count(),
            ],
            'by_type' => $this->getAnomalyTypeStats(),
            'affected_users' => LoginAnomaly::distinct('user_id')->where('created_at', '>=', now()->subDays(30))->count(),
            'unique_ips' => LoginAnomaly::distinct('ip_address')->where('created_at', '>=', now()->subDays(30))->count(),
            'blocked_ips' => BlockedIp::where('blocked_at', '>=', now()->subDays(30))->count(),
            'whitelisted_count' => LoginAnomaly::where('is_whitelisted', true)->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('admin.anomalies.risk-report', ['report' => $report]);
    }

    /**
     * ==================== HELPER METHODS ====================
     */
    protected function getStatistics()
    {
        return [
            'today' => LoginAnomaly::where('created_at', '>=', now()->startOfDay())->count(),
            'this_month' => LoginAnomaly::where('created_at', '>=', now()->startOfMonth())->count(),
            'critical_today' => LoginAnomaly::where('risk_level', 'critical')->where('created_at', '>=', now()->startOfDay())->count(),
            'total' => LoginAnomaly::count(),
            'unresolved' => LoginAnomaly::whereIn('status', ['new', 'under_investigation'])->count(),
            'blocked_users' => User::where('is_blocked', true)->count(),
        ];
    }

    protected function getRecentAnomalies($limit = 10)
    {
        return LoginAnomaly::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function getTopTargetedUsers($limit = 5)
    {
        return LoginAnomaly::selectRaw('user_id, COUNT(*) as anomaly_count, MAX(risk_level) as highest_risk')
            ->groupBy('user_id')
            ->orderBy('anomaly_count', 'desc')
            ->limit($limit)
            ->with('user')
            ->get();
    }

    protected function getRiskLevelBreakdown()
    {
        return [
            'critical' => LoginAnomaly::where('risk_level', 'critical')->count(),
            'high' => LoginAnomaly::where('risk_level', 'high')->count(),
            'medium' => LoginAnomaly::where('risk_level', 'medium')->count(),
            'low' => LoginAnomaly::where('risk_level', 'low')->count(),
        ];
    }

    protected function getAnomalyTrend($days = 30)
    {
        $data = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = LoginAnomaly::whereDate('created_at', $date->format('Y-m-d'))->count();
            $data[] = [
                'date' => $date->format('M d'),
                'count' => $count,
            ];
        }

        return $data;
    }

    protected function getTopAnomalyTypes($limit = 8)
    {
        return LoginAnomaly::selectRaw('anomaly_type, COUNT(*) as count')
            ->groupBy('anomaly_type')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function getGeoAnomalies($limit = 5)
    {
        return LoginAnomaly::selectRaw('country, COUNT(*) as count, MAX(risk_level) as risk_level')
            ->where('country', '!=', null)
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function getFailedLoginAttempts($limit = 5)
    {
        return LoginAnomaly::where('anomaly_type', 'failed_login')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function getUserAnomalyHistory($userId, $limit = 20)
    {
        return LoginAnomaly::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function blockIpSessions($ipAddress)
    {
        DB::table('user_sessions')
            ->where('ip_address', $ipAddress)
            ->where('status', 'active')
            ->update([
                'status' => 'terminated',
                'terminated_at' => now(),
                'termination_reason' => 'IP blocked due to anomaly',
            ]);
    }

    protected function getAnomalyTypeStats()
    {
        return LoginAnomaly::selectRaw('anomaly_type, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('anomaly_type')
            ->get()
            ->pluck('count', 'anomaly_type')
            ->toArray();
    }

    protected function getRiskColor($riskLevel)
    {
        return [
            'critical' => '#dc3545',
            'high' => '#fd7e14',
            'medium' => '#ffc107',
            'low' => '#28a745',
        ][$riskLevel] ?? '#6c757d';
    }
}
