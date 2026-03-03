<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BlockedIp;
use App\Models\ConcurrentLogin;
use App\Models\LoginAttempt;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SuspiciousLogin;
use App\Models\User;
use App\Models\UserSession;

class ComprehensiveDashboardController extends Controller
{
    /**
     * Display comprehensive admin dashboard with all metrics
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = $this->getStats();
        $topCities = $this->getTopCities();
        $cityStats = $this->getCityStats();
        $alerts = $this->getAlerts();
        $recentAlerts = $this->getRecentAlerts();
        $activityLogs = $this->getActivityLogs();
        $loginStats = $this->getLoginStats();
        $recentOrders = $this->getRecentOrders();
        $topProducts = $this->getTopProducts();
        $securityMetrics = $this->getSecurityMetrics();
        $anomalyData = $this->getAnomalyData();

        // Create overview with proper key names for the view
        $overview = [
            'todayRevenue' => $stats['revenue_today'],
            'todayOrders' => $stats['orders_today'],
            'totalCustomers' => $stats['total_users'],
            'activeUsers' => $stats['active_users'],
            'monthRevenue' => $stats['revenue_month'],
        ];

        // Create security alerts from recent suspicious logins and concurrent logins
        $securityAlerts = [];
        foreach ($securityMetrics['recent_suspicious'] as $suspicious) {
            $securityAlerts[] = [
                'type' => '🚨 Suspicious Login',
                'description' => "Weird login from {$suspicious->ip_address}",
                'count' => 1,
            ];
        }
        foreach ($securityMetrics['recent_concurrent'] as $concurrent) {
            $securityAlerts[] = [
                'type' => '⚠️ Concurrent Login',
                'description' => "Multiple devices detected",
                'count' => 1,
            ];
        }
        
        // Add summary alerts if any
        if ($securityMetrics['failed_attempts'] > 0) {
            $securityAlerts[] = [
                'type' => '❌ Failed Attempts',
                'description' => "Failed login attempts detected",
                'count' => $securityMetrics['failed_attempts'],
            ];
        }
        if ($securityMetrics['locked_accounts'] > 0) {
            $securityAlerts[] = [
                'type' => '🔒 Locked Accounts',
                'description' => "Accounts locked for security",
                'count' => $securityMetrics['locked_accounts'],
            ];
        }

        // Format top products for view
        $formattedTopProducts = [];
        foreach ($topProducts as $item) {
            $formattedTopProducts[] = [
                'name' => $item->product->name ?? 'Product',
                'sales' => $item->order_count ?? 0,
                'revenue' => ($item->product->price ?? 0) * ($item->total_qty ?? 0),
            ];
        }

        // Format recent orders for view
        $formattedRecentOrders = [];
        foreach ($recentOrders as $order) {
            $formattedRecentOrders[] = [
                'id' => $order->id,
                'user' => $order->user->name ?? 'Customer',
                'email' => $order->user->email ?? '',
                'total' => $order->total_amount ?? 0,
                'status' => $order->status ?? 'pending',
                'date' => $order->created_at->format('M d, Y'),
            ];
        }

        // Create user metrics
        $userMetrics = [
            'totalUsers' => $stats['total_users'],
            'newThisMonth' => $stats['new_users_month'],
            'activeThisWeek' => UserSession::whereNull('logged_out_at')->where('created_at', '>=', today()->subDays(7))->count(),
        ];

        // Create sales chart data (last 7 days)
        $salesChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $revenue = Order::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            $salesChart[$date->format('M d')] = $revenue;
        }

        // Reassign for view
        $recentOrders = $formattedRecentOrders;
        $topProducts = $formattedTopProducts;

        return view('admin.reports.dashboard', compact(
            'stats',
            'overview',
            'topCities',
            'cityStats',
            'alerts',
            'recentAlerts',
            'activityLogs',
            'loginStats',
            'recentOrders',
            'topProducts',
            'securityMetrics',
            'securityAlerts',
            'userMetrics',
            'salesChart',
            'anomalyData'
        ));
    }

    /**
     * Get dashboard statistics
     * @return array<string, int|float>
     */
    private function getStats(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_blocked', false)->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_month' => User::whereDate('created_at', '>=', today()->subDays(30))->count(),

            'total_orders' => Order::count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'pending_orders' => Order::whereIn('status', ['pending', 'processing'])->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),

            'total_products' => Product::count(),
            'active_products' => Product::where('stock', '>', 0)->count(),
            'low_stock' => Product::where('stock', '<', 10)->count(),

            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'revenue_today' => Order::where('status', 'completed')->whereDate('created_at', today())->sum('total_amount'),
            'revenue_month' => Order::where('status', 'completed')->whereDate('created_at', '>=', today()->subDays(30))->sum('total_amount'),

            'active_sessions' => UserSession::whereNull('logged_out_at')->count(),
            'concurrent_logins' => ConcurrentLogin::where('status', 'detected')->count(),
            'suspicious_activities' => SuspiciousLogin::where('confirmed_by_user', false)->count(),
            'blocked_ips' => BlockedIp::where('is_permanent', true)->orWhere('unblock_at', '>', now())->count(),
        ];
    }

    /**
     * Get top cities with active sessions
     */
    private function getTopCities()
    {
        return UserSession::whereNull('logged_out_at')
            ->whereNotNull('location')
            ->selectRaw('location, country, COUNT(*) as session_count')
            ->groupBy('location', 'country')
            ->orderByDesc('session_count')
            ->limit(10)
            ->get();
    }

    /**
     * Get city statistics
     */
    private function getCityStats()
    {
        return UserSession::whereNotNull('location')
            ->selectRaw('location, country, COUNT(DISTINCT user_id) as unique_users, COUNT(*) as total_sessions')
            ->groupBy('location', 'country')
            ->orderByDesc('total_sessions')
            ->limit(15)
            ->get();
    }

    /**
     * Get alert statistics
     * @return array<string, int>
     */
    private function getAlerts(): array
    {
        return [
            'critical_alerts' => Notification::where('severity', 'critical')->unread()->count(),
            'warning_alerts' => Notification::where('severity', 'warning')->unread()->count(),
            'info_alerts' => Notification::where('severity', 'info')->unread()->count(),
            'total_unread' => Notification::unread()->count(),
        ];
    }

    /**
     * Get recent alerts
     */
    private function getRecentAlerts()
    {
        return Notification::query()
            ->with('user')
            ->where('severity', '!=', 'message')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    /**
     * Get activity logs
     */
    private function getActivityLogs()
    {
        return ActivityLog::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    }

    /**
     * Get login statistics
     * @return array<string, int>
     */
    private function getLoginStats(): array
    {
        return [
            'today_logins' => LoginAttempt::whereDate('attempted_at', today())->where('success', true)->count(),
            'today_failed' => LoginAttempt::whereDate('attempted_at', today())->where('success', false)->count(),
            'month_logins' => LoginAttempt::whereDate('attempted_at', '>=', today()->subDays(30))->where('success', true)->count(),
            'month_failed' => LoginAttempt::whereDate('attempted_at', '>=', today()->subDays(30))->where('success', false)->count(),
        ];
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders()
    {
        return Order::with(['user', 'items'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    /**
     * Get top products
     */
    private function getTopProducts()
    {
        return OrderItem::selectRaw('product_id, COUNT(*) as order_count, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('order_count')
            ->limit(8)
            ->get();
    }

    /**
     * Get security metrics
     * @return array<string, mixed>
     */
    private function getSecurityMetrics(): array
    {
        // Get failed login attempts in last 24 hours
        $failedAttempts = LoginAttempt::where('success', false)
            ->where('attempted_at', '>=', now()->subHours(24))
            ->count();

        // Get accounts locked
        $lockedAccounts = User::where('is_blocked', true)->count();

        // Get concurrent logins detected
        $concurrentLogins = ConcurrentLogin::where('status', 'detected')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        // Get users with concurrent logins
        $usersWithConcurrent = ConcurrentLogin::where('status', 'detected')
            ->where('created_at', '>=', now()->subHours(24))
            ->distinct('user_id')
            ->count();

        // Get login anomalies
        $loginAnomalies = SuspiciousLogin::where('created_at', '>=', now()->subHours(24))
            ->count();

        return [
            'login_anomalies' => $loginAnomalies,
            'failed_attempts' => $failedAttempts,
            'locked_accounts' => $lockedAccounts,
            'concurrent_logins' => $concurrentLogins,
            'users_with_concurrent' => $usersWithConcurrent,
            'recent_concurrent' => ConcurrentLogin::where('status', 'detected')
                ->with('user')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
            'recent_suspicious' => SuspiciousLogin::where('confirmed_by_user', false)
                ->with('user')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
            'blocked_ips' => BlockedIp::where('is_permanent', true)
                ->orWhere('unblock_at', '>', now())
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Get anomaly data
     * @return array<string, mixed>
     */
    private function getAnomalyData(): array
    {
        return [
            'order_status_count' => Order::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'user_role_count' => User::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role')
                ->toArray(),
            'system_health' => [
                'database_check' => true,
                'cache_check' => true,
                'queue_check' => true,
                'storage_check' => true,
            ],
        ];
    }
}
