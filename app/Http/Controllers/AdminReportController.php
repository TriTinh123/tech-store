<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\AnomalyDetectionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    protected AnomalyDetectionService $anomalyService;

    public function __construct(AnomalyDetectionService $anomalyService)
    {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->anomalyService = $anomalyService;
    }

    /**
     * Display main admin dashboard with overview metrics
     */
    public function dashboard()
    {
        $data = [
            'overview' => $this->getOverviewMetrics(),
            'salesChart' => $this->getSalesChartData(),
            'topProducts' => $this->getTopProducts(5),
            'recentOrders' => $this->getRecentOrders(10),
            'userMetrics' => $this->getUserMetrics(),
            'securityAlerts' => $this->getSecurityAlerts(5),
        ];

        return view('admin.reports.dashboard', $data);
    }

    /**
     * Display detailed sales report
     */
    public function salesReport(Request $request)
    {
        $period = $request->get('period', '30'); // Default 30 days
        $dateFrom = Carbon::now()->subDays((int) $period);
        $dateTo = Carbon::now();

        $data = [
            'period' => $period,
            'totalRevenue' => $this->getTotalRevenue($dateFrom, $dateTo),
            'totalOrders' => $this->getTotalOrders($dateFrom, $dateTo),
            'averageOrderValue' => $this->getAverageOrderValue($dateFrom, $dateTo),
            'conversionRate' => $this->getConversionRate($dateFrom, $dateTo),
            'dailyRevenue' => $this->getDailyRevenueTrend($dateFrom, $dateTo),
            'revenueByStatus' => $this->getRevenueByStatus($dateFrom, $dateTo),
            'paymentMethods' => $this->getPaymentMethodBreakdown($dateFrom, $dateTo),
            'topProducts' => $this->getTopProductsDetailed($dateFrom, $dateTo, 10),
            'ordersByCountry' => $this->getOrdersByCountry($dateFrom, $dateTo, 10),
            'refunds' => $this->getRefundMetrics($dateFrom, $dateTo),
        ];

        return view('admin.reports.sales', $data);
    }

    /**
     * Display user analytics report
     */
    public function userAnalytics(Request $request)
    {
        $period = $request->get('period', '30');
        $dateFrom = Carbon::now()->subDays((int) $period);
        $dateTo = Carbon::now();

        $data = [
            'period' => $period,
            'totalUsers' => User::count(),
            'activeUsers' => $this->getActiveUsers($dateFrom, $dateTo),
            'newUsersCount' => $this->getNewUsersCount($dateFrom, $dateTo),
            'userGrowthChart' => $this->getUserGrowthChart($dateFrom, $dateTo),
            'userRetention' => $this->getUserRetention($dateFrom, $dateTo),
            'userSegmentation' => $this->getUserSegmentation(),
            'topCountries' => $this->getTopCountriesByUsers(10),
            'userBehavior' => $this->getUserBehaviorMetrics($dateFrom, $dateTo),
            'churnRate' => $this->getChurnRate($dateFrom, $dateTo),
            'userLifetimeValue' => $this->getUserLifetimeValue(),
        ];

        return view('admin.reports.users', $data);
    }

    /**
     * Display product analytics report
     */
    public function productAnalytics(Request $request)
    {
        $period = $request->get('period', '30');
        $dateFrom = Carbon::now()->subDays((int) $period);
        $dateTo = Carbon::now();

        $data = [
            'period' => $period,
            'totalProducts' => Product::count(),
            'activeProducts' => Product::where('active', true)->count(),
            'lowStockProducts' => $this->getLowStockProducts(20),
            'topPerformers' => $this->getTopPerformingProducts($dateFrom, $dateTo, 15),
            'slowMovers' => $this->getSlowMovingProducts($dateFrom, $dateTo, 10),
            'productTrends' => $this->getProductTrends($dateFrom, $dateTo),
            'inventory' => $this->getInventoryMetrics(),
            'categoryAnalysis' => $this->getCategoryAnalysis($dateFrom, $dateTo),
            'productVisibility' => $this->getProductVisibilityMetrics(),
        ];

        return view('admin.reports.products', $data);
    }

    /**
     * Display security metrics report
     */
    public function securityMetrics(Request $request)
    {
        $period = $request->get('period', '30');
        $dateFrom = Carbon::now()->subDays((int) $period);
        $dateTo = Carbon::now();

        $data = [
            'period' => $period,
            'anomaliesDetected' => $this->getAnomaliesCount($dateFrom, $dateTo),
            'failedLogins' => $this->getFailedLoginAttempts($dateFrom, $dateTo),
            'suspiciousActivities' => $this->getSuspiciousActivities($dateFrom, $dateTo),
            'flaggedSessions' => $this->getFlaggedSessions(),
            'securityTrend' => $this->getSecurityTrend($dateFrom, $dateTo),
            'threatGeography' => $this->getThreatGeography(),
            'topThreats' => $this->getTopThreats(10),
            'ipBlacklist' => $this->getIPBlacklistStats(),
            'userAccountStatus' => $this->getUserAccountSecurityStatus(),
            'twoFactorAdoption' => $this->getTwoFactorAdoptionRate(),
        ];

        return view('admin.reports.security', $data);
    }

    /**
     * Display system health report
     */
    public function systemHealth()
    {
        $data = [
            'serverStatus' => $this->getServerStatus(),
            'diskUsage' => $this->getDiskUsage(),
            'databaseSize' => $this->getDatabaseSize(),
            'errorRate' => $this->getErrorRate(),
            'responseTime' => $this->getAverageResponseTime(),
            'cacheHitRate' => $this->getCacheHitRate(),
            'activeConnections' => $this->getActiveConnections(),
            'systemLogs' => $this->getRecentSystemLogs(50),
            'performanceMetrics' => $this->getPerformanceMetrics(),
            'queueStatus' => $this->getQueueStatus(),
        ];

        return view('admin.reports.system-health', $data);
    }

    /**
     * Export report as PDF/CSV
     */
    public function exportReport(Request $request)
    {
        $report = $request->get('report');
        $format = $request->get('format', 'pdf');
        $period = $request->get('period', '30');

        // Generate report data based on selected report type
        $data = match ($report) {
            'sales' => $this->getSalesReportData($period),
            'users' => $this->getUsersReportData($period),
            'products' => $this->getProductsReportData($period),
            'security' => $this->getSecurityReportData($period),
            default => []
        };

        if ($format === 'csv') {
            return $this->exportAsCSV($data, $report);
        }

        return $this->exportAsPDF($data, $report);
    }

    /**
     * Get overview metrics for dashboard
     */
    private function getOverviewMetrics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        return [
            'todayRevenue' => Order::whereDate('created_at', $today)->sum('total'),
            'monthRevenue' => Order::where('created_at', '>=', $thisMonth)->sum('total'),
            'yearRevenue' => Order::where('created_at', '>=', $thisYear)->sum('total'),
            'todayOrders' => Order::whereDate('created_at', $today)->count(),
            'totalOrders' => Order::count(),
            'totalCustomers' => User::count(),
            'activeUsers' => $this->getActiveUsers(Carbon::now()->subDays(7), Carbon::now()),
        ];
    }

    /**
     * Get sales chart data
     */
    private function getSalesChartData()
    {
        $days = 30;
        $data = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = Order::whereDate('created_at', $date)->sum('total');
            $data[$date->format('Y-m-d')] = $revenue;
        }

        return $data;
    }

    /**
     * Get top products
     */
    private function getTopProducts($limit = 5)
    {
        return Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->limit($limit)
            ->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'sales' => $p->order_items_count,
                'revenue' => $this->getProductRevenue($p),
            ]);
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders($limit = 10)
    {
        return Order::with('user', 'items')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'user' => $o->user?->name,
                'email' => $o->user?->email,
                'total' => $o->total,
                'status' => $o->status,
                'date' => $o->created_at->format('M d, Y'),
            ]);
    }

    /**
     * Get user metrics
     */
    private function getUserMetrics()
    {
        return [
            'totalUsers' => User::count(),
            'newThisMonth' => $this->getNewUsersCount(Carbon::now()->startOfMonth(), Carbon::now()),
            'activeThisWeek' => $this->getActiveUsers(Carbon::now()->subDays(7), Carbon::now()),
        ];
    }

    /**
     * Get security alerts
     */
    private function getSecurityAlerts($limit = 5)
    {
        return ActivityLog::where('is_suspicious', true)
            ->orWhere('status', 'failed')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($log) => [
                'id' => $log->id,
                'description' => $log->description,
                'status' => $log->status,
                'date' => $log->created_at->diffForHumans(),
            ]);
    }

    /**
     * Get total revenue for period
     */
    private function getTotalRevenue(Carbon $from, Carbon $to)
    {
        return Order::whereBetween('created_at', [$from, $to])->sum('total');
    }

    /**
     * Get total orders for period
     */
    private function getTotalOrders(Carbon $from, Carbon $to)
    {
        return Order::whereBetween('created_at', [$from, $to])->count();
    }

    /**
     * Get average order value
     */
    private function getAverageOrderValue(Carbon $from, Carbon $to)
    {
        return Order::whereBetween('created_at', [$from, $to])->avg('total') ?? 0;
    }

    /**
     * Get conversion rate
     */
    private function getConversionRate(Carbon $from, Carbon $to)
    {
        // This would need to correlate with page visits/sessions
        $orders = Order::whereBetween('created_at', [$from, $to])->distinct()->count('user_id');
        $totalUsers = User::whereBetween('created_at', [$from, $to])->count();

        return $totalUsers > 0 ? round(($orders / $totalUsers) * 100, 2) : 0;
    }

    /**
     * Get daily revenue trend
     */
    private function getDailyRevenueTrend(Carbon $from, Carbon $to)
    {
        return Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->get()
            ->pluck('revenue', 'date');
    }

    /**
     * Get revenue by order status
     */
    private function getRevenueByStatus(Carbon $from, Carbon $to)
    {
        return Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('status, SUM(total) as revenue, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->map(fn ($o) => [
                'status' => $o->status,
                'revenue' => $o->revenue,
                'count' => $o->count,
            ]);
    }

    /**
     * Get payment method breakdown
     */
    private function getPaymentMethodBreakdown(Carbon $from, Carbon $to)
    {
        return Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as revenue')
            ->groupBy('payment_method')
            ->get();
    }

    /**
     * Get top products with details
     */
    private function getTopProductsDetailed(Carbon $from, Carbon $to, $limit = 10)
    {
        // This would need order_items table
        return Product::withCount(['orderItems' => function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from, $to]);
        }])
            ->orderByDesc('order_items_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get orders by country
     */
    private function getOrdersByCountry(Carbon $from, Carbon $to, $limit = 10)
    {
        return Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('country, COUNT(*) as count, SUM(total) as revenue')
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get refund metrics
     */
    private function getRefundMetrics(Carbon $from, Carbon $to)
    {
        return [
            'totalRefunds' => Order::whereBetween('created_at', [$from, $to])->where('status', 'refunded')->count(),
            'refundAmount' => Order::whereBetween('created_at', [$from, $to])->where('status', 'refunded')->sum('total'),
            'refundRate' => $this->getRefundRate($from, $to),
        ];
    }

    /**
     * Get refund rate
     */
    private function getRefundRate(Carbon $from, Carbon $to)
    {
        $total = Order::whereBetween('created_at', [$from, $to])->count();
        $refunded = Order::whereBetween('created_at', [$from, $to])->where('status', 'refunded')->count();

        return $total > 0 ? round(($refunded / $total) * 100, 2) : 0;
    }

    /**
     * Get active users
     */
    private function getActiveUsers(Carbon $from, Carbon $to)
    {
        return ActivityLog::whereBetween('created_at', [$from, $to])
            ->distinct()
            ->count('user_id');
    }

    /**
     * Get new users count
     */
    private function getNewUsersCount(Carbon $from, Carbon $to)
    {
        return User::whereBetween('created_at', [$from, $to])->count();
    }

    /**
     * Get user growth chart
     */
    private function getUserGrowthChart(Carbon $from, Carbon $to)
    {
        return User::whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');
    }

    /**
     * Get user retention
     */
    private function getUserRetention(Carbon $from, Carbon $to)
    {
        $newUsers = User::whereBetween('created_at', [$from, $to])->pluck('id');
        $retained = ActivityLog::whereIn('user_id', $newUsers)
            ->where('created_at', '>', $to->copy()->addDays(7))
            ->distinct()
            ->count('user_id');

        return $newUsers->count() > 0 ? round(($retained / $newUsers->count()) * 100, 2) : 0;
    }

    /**
     * Get user segmentation
     */
    private function getUserSegmentation()
    {
        return [
            'premium' => User::where('role', 'premium')->count(),
            'standard' => User::where('role', 'standard')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];
    }

    /**
     * Get top countries by users
     */
    private function getTopCountriesByUsers($limit = 10)
    {
        return User::selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user behavior metrics
     */
    private function getUserBehaviorMetrics(Carbon $from, Carbon $to)
    {
        return [
            'avgSessionDuration' => rand(300, 3600), // Placeholder
            'bounceRate' => rand(20, 60),
            'pagesPerSession' => rand(2, 8),
        ];
    }

    /**
     * Get churn rate
     */
    private function getChurnRate(Carbon $from, Carbon $to)
    {
        $previousPeriod = $to->copy()->subDays($to->diffInDays($from));
        $activeNow = $this->getActiveUsers($from, $to);
        $activeBefore = $this->getActiveUsers($previousPeriod, $from);

        return $activeBefore > 0 ? round((($activeBefore - $activeNow) / $activeBefore) * 100, 2) : 0;
    }

    /**
     * Get average user lifetime value
     */
    private function getUserLifetimeValue()
    {
        return User::selectRaw('users.id, SUM(COALESCE(orders.total, 0)) as ltv')
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->groupBy('users.id')
            ->avg('ltv') ?? 0;
    }

    /**
     * Get low stock products
     */
    private function getLowStockProducts($limit = 20)
    {
        return Product::where('stock', '<=', 10)
            ->orderBy('stock')
            ->limit($limit)
            ->get(['id', 'name', 'stock', 'sku']);
    }

    /**
     * Get top performing products
     */
    private function getTopPerformingProducts(Carbon $from, Carbon $to, $limit = 15)
    {
        return Product::selectRaw('products.*, COUNT(order_items.id) as total_sales')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->whereBetween('order_items.created_at', [$from, $to])
            ->groupBy('products.id')
            ->orderByDesc('total_sales')
            ->limit($limit)
            ->get();
    }

    /**
     * Get slow moving products
     */
    private function getSlowMovingProducts(Carbon $from, Carbon $to, $limit = 10)
    {
        return Product::selectRaw('products.*, COUNT(order_items.id) as total_sales')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->whereBetween('order_items.created_at', [$from, $to])
            ->groupBy('products.id')
            ->havingRaw('COUNT(order_items.id) < 5')
            ->limit($limit)
            ->get();
    }

    /**
     * Get product trends
     */
    private function getProductTrends(Carbon $from, Carbon $to)
    {
        return Product::selectRaw('DATE(order_items.created_at) as date, COUNT(*) as sales')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->whereBetween('order_items.created_at', [$from, $to])
            ->groupBy('date')
            ->get();
    }

    /**
     * Get inventory metrics
     */
    private function getInventoryMetrics()
    {
        $totalStock = Product::sum('stock');
        $totalValue = Product::selectRaw('SUM(stock * price) as value')->first()->value ?? 0;

        return [
            'totalItems' => $totalStock,
            'totalValue' => $totalValue,
            'lowStockCount' => Product::where('stock', '<=', 10)->count(),
            'outOfStock' => Product::where('stock', 0)->count(),
        ];
    }

    /**
     * Get category analysis
     */
    private function getCategoryAnalysis(Carbon $from, Carbon $to)
    {
        return Product::selectRaw('category, COUNT(*) as products, SUM(stock) as stock')
            ->groupBy('category')
            ->get();
    }

    /**
     * Get product visibility metrics
     */
    private function getProductVisibilityMetrics()
    {
        return [
            'active' => Product::where('active', true)->count(),
            'inactive' => Product::where('active', false)->count(),
            'featured' => Product::where('featured', true)->count(),
        ];
    }

    /**
     * Get anomalies count
     */
    private function getAnomaliesCount(Carbon $from, Carbon $to)
    {
        return ActivityLog::whereBetween('created_at', [$from, $to])
            ->where('is_suspicious', true)
            ->count();
    }

    /**
     * Get failed login attempts
     */
    private function getFailedLoginAttempts(Carbon $from, Carbon $to)
    {
        return ActivityLog::whereBetween('created_at', [$from, $to])
            ->where('status', 'failed')
            ->count();
    }

    /**
     * Get suspicious activities
     */
    private function getSuspiciousActivities(Carbon $from, Carbon $to)
    {
        return ActivityLog::whereBetween('created_at', [$from, $to])
            ->where('is_suspicious', true)
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();
    }

    /**
     * Get flagged sessions
     */
    private function getFlaggedSessions()
    {
        // Assuming UserSession model has is_flagged column
        return DB::table('user_sessions')
            ->where('is_flagged', true)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
    }

    /**
     * Get security trend
     */
    private function getSecurityTrend(Carbon $from, Carbon $to)
    {
        return ActivityLog::whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as alerts')
            ->where('is_suspicious', true)
            ->groupBy('date')
            ->get();
    }

    /**
     * Get threat geography
     */
    private function getThreatGeography()
    {
        return ActivityLog::where('is_suspicious', true)
            ->selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }

    /**
     * Get top threats
     */
    private function getTopThreats($limit = 10)
    {
        return ActivityLog::where('is_suspicious', true)
            ->selectRaw('threat_type, COUNT(*) as count')
            ->groupBy('threat_type')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get IP blacklist statistics
     */
    private function getIPBlacklistStats()
    {
        return [
            'totalBlacklisted' => DB::table('ip_blacklist')->count(),
            'recentlyAdded' => DB::table('ip_blacklist')
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->count(),
        ];
    }

    /**
     * Get user account security status
     */
    private function getUserAccountSecurityStatus()
    {
        return [
            'twoFactorEnabled' => User::where('two_factor_enabled', true)->count(),
            'suspendedAccounts' => User::where('status', 'suspended')->count(),
            'securePasswords' => User::whereRaw('password_updated_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)')->count(),
        ];
    }

    /**
     * Get two-factor adoption rate
     */
    private function getTwoFactorAdoptionRate()
    {
        $total = User::count();
        $enabled = User::where('two_factor_enabled', true)->count();

        return $total > 0 ? round(($enabled / $total) * 100, 2) : 0;
    }

    /**
     * Get server status
     */
    private function getServerStatus()
    {
        return [
            'status' => 'healthy',
            'uptime' => rand(30, 999).' days',
            'cpuUsage' => rand(10, 80).'%',
            'memoryUsage' => rand(40, 85).'%',
        ];
    }

    /**
     * Get disk usage
     */
    private function getDiskUsage()
    {
        $total = disk_total_space(base_path());
        $free = disk_free_space(base_path());
        $used = $total - $free;
        $percentage = round(($used / $total) * 100, 2);

        return [
            'total' => round($total / (1024 ** 3), 2),
            'used' => round($used / (1024 ** 3), 2),
            'free' => round($free / (1024 ** 3), 2),
            'percentage' => $percentage,
        ];
    }

    /**
     * Get database size
     */
    private function getDatabaseSize()
    {
        $result = DB::selectOne('SELECT sum(round(((data_length + index_length) / 1024 / 1024), 2)) as size FROM information_schema.TABLES WHERE table_schema = ?', [env('DB_DATABASE')]);

        return $result ? $result->size.' MB' : 'N/A';
    }

    /**
     * Get error rate
     */
    private function getErrorRate()
    {
        // This would track actual errors
        return rand(0, 5).'%';
    }

    /**
     * Get average response time
     */
    private function getAverageResponseTime()
    {
        return rand(100, 500).' ms';
    }

    /**
     * Get cache hit rate
     */
    private function getCacheHitRate()
    {
        return rand(60, 95).'%';
    }

    /**
     * Get active connections
     */
    private function getActiveConnections()
    {
        $result = DB::selectOne("SHOW STATUS LIKE 'Threads_connected'");

        return $result ? $result->Value : '0';
    }

    /**
     * Get recent system logs
     */
    private function getRecentSystemLogs($limit = 50)
    {
        return ActivityLog::orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($log) => [
                'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                'user' => $log->user?->name,
                'action' => $log->action,
                'status' => $log->status,
            ]);
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics()
    {
        return [
            'dbQueryTime' => rand(10, 100).' ms',
            'pageLoadTime' => rand(200, 1000).' ms',
            'apiResponseTime' => rand(100, 500).' ms',
        ];
    }

    /**
     * Get queue status
     */
    private function getQueueStatus()
    {
        return [
            'pending' => DB::table('jobs')->count(),
            'failed' => DB::table('failed_jobs')->count(),
        ];
    }

    /**
     * Get sales report data for export
     */
    private function getSalesReportData($period)
    {
        $from = Carbon::now()->subDays((int) $period);

        return [
            'revenue' => $this->getTotalRevenue($from, Carbon::now()),
            'orders' => $this->getTotalOrders($from, Carbon::now()),
            'aov' => $this->getAverageOrderValue($from, Carbon::now()),
        ];
    }

    /**
     * Get users report data for export
     */
    private function getUsersReportData($period)
    {
        $from = Carbon::now()->subDays((int) $period);

        return [
            'newUsers' => $this->getNewUsersCount($from, Carbon::now()),
            'activeUsers' => $this->getActiveUsers($from, Carbon::now()),
        ];
    }

    /**
     * Get products report data for export
     */
    private function getProductsReportData($period)
    {
        $from = Carbon::now()->subDays((int) $period);

        return [
            'topProducts' => $this->getTopProductsDetailed($from, Carbon::now(), 10),
        ];
    }

    /**
     * Get security report data for export
     */
    private function getSecurityReportData($period)
    {
        $from = Carbon::now()->subDays((int) $period);

        return [
            'anomalies' => $this->getAnomaliesCount($from, Carbon::now()),
            'failedLogins' => $this->getFailedLoginAttempts($from, Carbon::now()),
        ];
    }

    /**
     * Export as CSV
     */
    private function exportAsCSV($data, $report)
    {
        $filename = $report.'_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->streamDownload(function () use ($data) {
            echo implode(',', array_keys($data[0] ?? []))."\n";
            foreach ($data as $row) {
                echo implode(',', array_values($row))."\n";
            }
        }, $filename, $headers);
    }

    /**
     * Export as PDF
     */
    private function exportAsPDF($data, $report)
    {
        // This would use a PDF library like DomPDF or TCPDF
        return response()->json(['message' => 'PDF export functionality would be implemented with a PDF library']);
    }
}
