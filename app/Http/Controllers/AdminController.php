<?php

namespace App\Http\Controllers;

use App\Events\OrderShipped;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $totalRevenue = Order::sum('total_amount');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 'user')->count();

        $recentOrders = Order::latest()->take(5)->get();
        $topProducts = Product::orderBy('rating', 'desc')->take(5)->get();
        $monthlyRevenue = Order::whereYear('created_at', date('Y'))
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->get();

        return view('dashboard', [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalUsers' => $totalUsers,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'monthlyRevenue' => $monthlyRevenue,
        ]);
    }

    /**
     * Show products list
     */
    public function products()
    {
        $products = Product::with('categoryModel')->paginate(15);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show create product form
     */
    public function createProduct()
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store new product
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'manufacturer' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $validated['image'] = '/images/'.$filename;
        }

        Product::create($validated);

        return redirect()->route('admin.products')->with('success', 'Sản phẩm đã được thêm');
    }

    /**
     * Show edit product form
     */
    public function editProduct(Product $product)
    {
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product
     */
    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug,'.$product->id,
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'manufacturer' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $validated['image'] = '/images/'.$filename;
        }

        $product->update($validated);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    /**
     * Delete product
     */
    public function deleteProduct(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Sản phẩm đã bị xóa');
    }

    /**
     * Show categories list
     */
    public function categories()
    {
        $categories = Category::paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show create category form
     */
    public function createCategory()
    {
        return view('admin.categories.create');
    }

    /**
     * Store new category
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'slug' => 'required|string|unique:categories',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories')->with('success', 'Danh mục đã được thêm');
    }

    /**
     * Show edit category form
     */
    public function editCategory(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update category
     */
    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'slug' => 'required|string|unique:categories,slug,'.$category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories')->with('success', 'Danh mục đã được cập nhật');
    }

    /**
     * Delete category
     */
    public function deleteCategory(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories')->with('success', 'Danh mục đã bị xóa');
    }

    /**
     * Show orders list
     */
    public function orders()
    {
        $orders = Order::with('user')->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show order detail
     */
    public function orderDetail(Order $order)
    {
        $order->load('items', 'user');

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Trạng thái đơn hàng đã được cập nhật');
    }

    /**
     * Show users list
     */
    public function users()
    {
        $users = User::where('role', 'user')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Block/Unblock user
     */
    public function toggleUserStatus(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Không thể khóa tài khoản admin');
        }

        $user->update(['is_blocked' => ! $user->is_blocked]);
        $status = $user->is_blocked ? 'khóa' : 'mở khóa';

        return redirect()->back()->with('success', 'Tài khoản đã được '.$status);
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Không thể xóa tài khoản admin');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Người dùng đã bị xóa');
    }

    // ==================== COUPONS MANAGEMENT ====================

    /**
     * Show coupons list
     */
    public function coupons()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show create coupon form
     */
    public function createCoupon()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store new coupon
     */
    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        Coupon::create(array_merge($validated, [
            'code' => strtoupper($validated['code']),
            'is_active' => $request->has('is_active'),
        ]));

        return redirect()->route('admin.coupons')->with('success', 'Mã giảm giá đã tạo thành công');
    }

    /**
     * Show edit coupon form
     */
    public function editCoupon(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update coupon
     */
    public function updateCoupon(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        $coupon->update(array_merge($validated, [
            'is_active' => $request->has('is_active'),
        ]));

        return redirect()->route('admin.coupons')->with('success', 'Mã giảm giá đã cập nhật');
    }

    /**
     * Delete coupon
     */
    public function deleteCoupon(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons')->with('success', 'Mã giảm giá đã xóa');
    }

    // ==================== RETURNS MANAGEMENT ====================

    /**
     * Show returns list
     */
    public function returns()
    {
        $returns = ReturnRequest::with('order', 'orderItem')->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.returns.index', compact('returns'));
    }

    /**
     * Show return detail
     */
    public function returnDetail(ReturnRequest $return)
    {
        return view('admin.returns.show', compact('return'));
    }

    /**
     * Approve return request
     */
    public function approveReturn(ReturnRequest $return)
    {
        $return->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Yêu cầu hoàn trả đã được phê duyệt');
    }

    /**
     * Reject return request
     */
    public function rejectReturn(Request $request, ReturnRequest $return)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        $return->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->back()->with('success', 'Yêu cầu hoàn trả đã bị từ chối');
    }

    /**
     * Mark return as completed
     */
    public function completeReturn(ReturnRequest $return)
    {
        $return->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Yêu cầu hoàn trả đã hoàn thành');
    }

    // ==================== SHIPPING MANAGEMENT ====================

    /**
     * Show orders for shipping tracking updates
     */
    public function shippingOrders()
    {
        $orders = Order::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.shipping.index', compact('orders'));
    }

    /**
     * Show update shipping form
     */
    public function updateShippingForm(Order $order)
    {
        return view('admin.shipping.edit', compact('order'));
    }

    /**
     * Update shipping tracking
     */
    public function updateShipping(Request $request, Order $order)
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string|max:100',
            'shipping_status' => 'required|in:pending,processing,shipped,out_for_delivery,delivered,returned',
            'shipping_provider' => 'required|string|max:50',
        ]);

        // Store old status to check if changed
        $oldStatus = $order->shipping_status;

        $order->update($validated);

        // Dispatch event only if shipping status changed to shipped or beyond
        if ($oldStatus !== $validated['shipping_status'] && in_array($validated['shipping_status'], ['shipped', 'out_for_delivery', 'delivered'])) {
            OrderShipped::dispatch($order);
        }

        return redirect()->route('admin.shipping.orders')->with('success', 'Thông tin vận chuyển đã cập nhật');
    }

    /**
     * Show security monitoring dashboard
     */
    public function securityMonitoring()
    {
        // Basic user statistics
        $totalUsers = User::count();

        // Suspicious login statistics
        $suspiciousCount = \App\Models\SuspiciousLogin::count();
        $highRiskCount = \App\Models\SuspiciousLogin::where('risk_level', 'high')
            ->orWhere('risk_level', 'critical')
            ->count();
        $criticalRiskCount = \App\Models\SuspiciousLogin::where('risk_level', 'critical')->count();

        // Security alerts statistics
        $totalAlerts = \App\Models\SecurityAlert::count();
        $unreadAlerts = \App\Models\SecurityAlert::unread()->count();
        $criticalAlerts = \App\Models\SecurityAlert::bySeverity('critical')->count();

        // Account lockout statistics
        $lockedAccounts = 0;
        $lockedUsers = [];

        foreach (User::all() as $user) {
            $failedAttempts = \App\Models\LoginAttempt::where('user_id', $user->id)
                ->where('success', false)
                ->where('attempted_at', '>=', now()->subMinutes(15))
                ->count();

            if ($failedAttempts >= 5) {
                $lockedAccounts++;
                $lockedUsers[] = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'failedAttempts' => $failedAttempts,
                    'lockedUntil' => \App\Models\LoginAttempt::where('user_id', $user->id)
                        ->where('success', false)
                        ->latest('attempted_at')
                        ->first()?->attempted_at?->addMinutes(15),
                ];
            }
        }

        // Active sessions
        $activeSessions = \App\Models\UserSession::where('status', 'active')->count();
        $uniqueIps = \App\Models\UserSession::where('status', 'active')->distinct('ip_address')->count();

        // Recent login attempts
        $recentFailedAttempts = \App\Models\LoginAttempt::where('success', false)
            ->where('attempted_at', '>=', now()->subHours(24))
            ->count();
        $recentSuccessfulAttempts = \App\Models\LoginAttempt::where('success', true)
            ->where('attempted_at', '>=', now()->subHours(24))
            ->count();

        // Suspicious logins - recent
        $suspiciousLogins = \App\Models\SuspiciousLogin::with('user')
            ->latest()
            ->take(20)
            ->get();

        // Recent security alerts
        $recentAlerts = \App\Models\SecurityAlert::with(['user', 'suspiciousLogin'])
            ->latest()
            ->take(15)
            ->get();

        // Top risky IPs
        $topRiskyIps = \App\Models\SuspiciousLogin::where('risk_level', 'high')
            ->orWhere('risk_level', 'critical')
            ->selectRaw('ip_address, COUNT(*) as count, risk_level')
            ->groupBy('ip_address', 'risk_level')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Top risky locations
        $topRiskyLocations = \App\Models\SuspiciousLogin::where('risk_level', 'high')
            ->orWhere('risk_level', 'critical')
            ->selectRaw('city, country, COUNT(*) as count')
            ->groupBy('city', 'country')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Security events timeline (last 24 hours)
        $timeline24h = collect();
        for ($i = 0; $i < 24; $i++) {
            $hour = now()->subHours(23 - $i)->startOfHour();
            $timeline24h->push([
                'time' => $hour->format('H:00'),
                'alerts' => \App\Models\SecurityAlert::where('created_at', '>=', $hour)
                    ->where('created_at', '<', $hour->addHour())
                    ->count(),
                'suspicious' => \App\Models\SuspiciousLogin::where('created_at', '>=', $hour)
                    ->where('created_at', '<', $hour)
                    ->count(),
            ]);
        }

        // Risk level breakdown (pie chart data)
        $riskLevelBreakdown = \App\Models\SuspiciousLogin::selectRaw('risk_level, COUNT(*) as count')
            ->groupBy('risk_level')
            ->get()
            ->keyBy('risk_level');

        // Alert type breakdown
        $alertTypeBreakdown = \App\Models\SecurityAlert::selectRaw('alert_type, COUNT(*) as count')
            ->groupBy('alert_type')
            ->get()
            ->keyBy('alert_type');

        // Users with most suspicious activity
        $topSuspiciousUsers = \App\Models\SuspiciousLogin::selectRaw('user_id, COUNT(*) as count')
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        return view('admin.security-monitoring', [
            'totalUsers' => $totalUsers,
            'suspiciousCount' => $suspiciousCount,
            'highRiskCount' => $highRiskCount,
            'criticalRiskCount' => $criticalRiskCount,
            'totalAlerts' => $totalAlerts,
            'unreadAlerts' => $unreadAlerts,
            'criticalAlerts' => $criticalAlerts,
            'lockedAccounts' => $lockedAccounts,
            'lockedUsers' => $lockedUsers,
            'activeSessions' => $activeSessions,
            'uniqueIps' => $uniqueIps,
            'recentFailedAttempts' => $recentFailedAttempts,
            'recentSuccessfulAttempts' => $recentSuccessfulAttempts,
            'suspiciousLogins' => $suspiciousLogins,
            'recentAlerts' => $recentAlerts,
            'topRiskyIps' => $topRiskyIps,
            'topRiskyLocations' => $topRiskyLocations,
            'timeline24h' => $timeline24h,
            'riskLevelBreakdown' => $riskLevelBreakdown,
            'alertTypeBreakdown' => $alertTypeBreakdown,
            'topSuspiciousUsers' => $topSuspiciousUsers,
        ]);
    }
}
