<?php

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\OrderReturn;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard(): \Illuminate\View\View
    {
        $stats   = $this->getDashboardStats();
        $reviews = $this->getDashboardReviews();
        $charts  = $this->getDashboardCharts();

        return view('admin.dashboard', array_merge($stats, $reviews, $charts));
    }

    private function getDashboardStats(): array
    {
        return [
            'totalRevenue'  => Order::sum('total_amount'),
            'totalOrders'   => Order::count(),
            'totalProducts' => Product::count(),
            'totalUsers'    => User::where('role', 'user')->count(),
            'recentOrders'  => Order::latest()->take(5)->get(),
            'topProducts'   => Product::orderBy('rating', 'desc')->take(5)->get(),
            'monthlyRevenue'=> Order::whereYear('created_at', date('Y'))
                ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
                ->groupBy('month')->get(),
        ];
    }

    private function getDashboardReviews(): array
    {
        $ratingDist = Review::selectRaw('rating, count(*) as cnt')
            ->groupBy('rating')->orderBy('rating')->pluck('cnt', 'rating')->toArray();
        $ratingDistFull = [];
        for ($s = 5; $s >= 1; $s--) {
            $ratingDistFull[$s] = $ratingDist[$s] ?? 0;
        }
        return [
            'totalReviews'  => Review::count(),
            'avgRating'     => round(Review::avg('rating') ?? 0, 1),
            'ratingDistFull'=> $ratingDistFull,
            'reviewsByDay'  => Review::where('created_at', '>=', now()->subDays(6))
                ->selectRaw('DATE(created_at) as day, count(*) as cnt, AVG(rating) as avg_rating')
                ->groupBy('day')->orderBy('day')->get()
                ->map(fn ($r) => ['day' => $r->day, 'cnt' => (int) $r->cnt, 'avg_rating' => round($r->avg_rating, 1)])
                ->values()->toArray(),
            'recentReviews' => Review::with('product')->latest()->take(5)->get(),
        ];
    }

    private function getDashboardCharts(): array
    {
        return [
            'ordersByStatus'     => Order::selectRaw('status, count(*) as cnt')
                ->groupBy('status')->pluck('cnt', 'status')->toArray(),
            'revenueByDay'       => Order::where('created_at', '>=', now()->subDays(6))
                ->selectRaw('DATE(created_at) as day, SUM(total_amount) as revenue')
                ->groupBy('day')->orderBy('day')->get()
                ->map(fn ($r) => ['day' => $r->day, 'revenue' => (float) $r->revenue])
                ->values()->toArray(),
            'productsByCategory' => \DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->selectRaw('categories.name as cat, count(*) as cnt')
                ->groupBy('categories.id', 'categories.name')
                ->pluck('cnt', 'cat')->toArray(),
            'usersByDay'         => User::where('role', 'user')
                ->where('created_at', '>=', now()->subDays(6))
                ->selectRaw('DATE(created_at) as day, count(*) as cnt')
                ->groupBy('day')->orderBy('day')->get()
                ->map(fn ($r) => ['day' => $r->day, 'cnt' => (int) $r->cnt])
                ->values()->toArray(),
        ];
    }

    /**
     * Show products list
     */
    public function products()
    {
        $products = Product::with('categoryModel')->paginate(15);
        $categories = Category::all();
        $totalProducts  = Product::count();
        $inStock        = Product::where('stock', '>', 0)->count();
        $outOfStock     = Product::where('stock', 0)->count();
        $featuredCount  = Product::where('is_featured', true)->count();
        $productsByCategory = \DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as cat, count(*) as cnt')
            ->groupBy('categories.id', 'categories.name')
            ->pluck('cnt', 'cat')->toArray();
        $stockData = [
            'in'       => $inStock,
            'out'      => $outOfStock,
            'featured' => $featuredCount,
            'regular'  => $totalProducts - $featuredCount,
        ];
        return view('admin.products.index', compact(
            'products', 'categories',
            'totalProducts', 'inStock', 'outOfStock', 'featuredCount',
            'productsByCategory', 'stockData'
        ));
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
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $validated['image'] = '/images/' . $filename;
        }

        Product::create($validated);
        return redirect()->route('admin.products')->with('success', 'Product added successfully');
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
            'slug' => 'required|string|unique:products,slug,' . $product->id,
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
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $validated['image'] = '/images/' . $filename;
        }

        $product->update($validated);
        return redirect()->route('admin.products.edit', $product)->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product
     */
    public function deleteProduct(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted');
    }

    /**
     * Show categories list
     */
    public function categories()
    {
        $categories       = Category::paginate(15);
        $totalCategories  = Category::count();
        $withProducts     = Category::has('products')->count();
        $emptyCategories  = Category::doesntHave('products')->count();
        $topCat           = Category::withCount('products')->orderByDesc('products_count')->first();
        $topCategory      = $topCat ? $topCat->name : '—';
        $perCategoryData  = Category::withCount('products')->get()->pluck('products_count', 'name')->toArray();
        return view('admin.categories.index', compact(
            'categories', 'totalCategories', 'withProducts',
            'emptyCategories', 'topCategory', 'perCategoryData'
        ));
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
        return redirect()->route('admin.categories')->with('success', 'Category added');
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
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'required|string|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        $category->update($validated);
        return redirect()->route('admin.categories')->with('success', 'Category updated');
    }

    /**
     * Delete category
     */
    public function deleteCategory(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories')->with('success', 'Category deleted');
    }

    /**
     * Show orders list
     */
    public function orders(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');
        $query  = Order::with('user')->latest();
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }
        $orders = $query->paginate(20)->withQueryString();
        $counts = Order::selectRaw('status, count(*) as cnt')
            ->groupBy('status')->pluck('cnt', 'status')->toArray();
        return view('admin.orders.index', compact('orders', 'counts', 'status', 'search'));
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
            'status'            => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            'tracking_number'   => 'nullable|string|max:100',
            'shipping_provider' => 'nullable|string|max:100',
        ]);

        $update = ['status' => $validated['status']];
        if (!empty($validated['tracking_number']))  $update['tracking_number']   = $validated['tracking_number'];
        if (!empty($validated['shipping_provider'])) $update['shipping_provider'] = $validated['shipping_provider'];

        $order->update($update);
        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated');
    }

    /**
     * Show users list
     */
    public function users()
    {
        $users        = User::where('role', 'user')->paginate(15);
        $totalUsers   = User::where('role', 'user')->count();
        $activeUsers  = User::where('role', 'user')->where('is_blocked', false)->count();
        $blockedUsers = User::where('role', 'user')->where('is_blocked', true)->count();
        $newThisWeek  = User::where('role', 'user')->where('created_at', '>=', now()->subDays(7))->count();
        $usersByDay   = User::where('role', 'user')
            ->where('created_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(created_at) as day, count(*) as cnt')
            ->groupBy('day')->orderBy('day')->get()
            ->map(fn($r) => ['day' => $r->day, 'cnt' => (int)$r->cnt])->values()->toArray();
        $userStats = ['active' => $activeUsers, 'blocked' => $blockedUsers];
        return view('admin.users.index', compact(
            'users', 'totalUsers', 'activeUsers', 'blockedUsers',
            'newThisWeek', 'usersByDay', 'userStats'
        ));
    }

    /**
     * Block/Unblock user
     */
    public function toggleUserStatus(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Cannot lock admin account');
        }

        $user->update(['is_blocked' => !$user->is_blocked]);
        $status = $user->is_blocked ? 'locked' : 'unlocked';
        return redirect()->back()->with('success', 'Account has been ' . $status);
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Cannot delete admin account');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted');
    }

    /**
     * AI / 3FA security audit log
     */
    public function securityLog()
    {
        $attempts = LoginAttempt::with('user')
            ->latest()
            ->paginate(25);

        $stats = [
            'total'        => LoginAttempt::count(),
            'anomaly'      => LoginAttempt::where('is_anomaly', true)->count(),
            'failed'       => LoginAttempt::where('success', false)->count(),
            'required_3fa' => LoginAttempt::where('required_3fa', true)->count(),
        ];

        // Chart data: risk level distribution
        $riskDist = LoginAttempt::selectRaw('risk_level, count(*) as cnt')
            ->whereNotNull('risk_level')
            ->groupBy('risk_level')
            ->pluck('cnt', 'risk_level')
            ->toArray();

        // Chart data: success vs failed per day (last 7 days)
        $daily = LoginAttempt::selectRaw("DATE(created_at) as day, SUM(CASE WHEN success=1 THEN 1 ELSE 0 END) as ok, SUM(CASE WHEN success=0 THEN 1 ELSE 0 END) as fail")
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupByRaw('DATE(created_at)')
            ->orderBy('day')
            ->get();

        // Chart data: logins by hour of day
        $byHour = LoginAttempt::selectRaw('HOUR(created_at) as hr, count(*) as cnt')
            ->groupByRaw('HOUR(created_at)')
            ->orderBy('hr')
            ->pluck('cnt', 'hr')
            ->toArray();
        $hourData = array_map(fn($h) => $byHour[$h] ?? 0, range(0, 23));

        return view('admin.security.index', compact('attempts', 'stats', 'riskDist', 'daily', 'hourData'));
    }

    // ─── AI Demo ──────────────────────────────────────────────────────────────

    public function aiDemo()
    {
        $aiUrl    = config('services.python_ai.url', 'http://127.0.0.1:5001');
        $demoData = null;
        $aiOnline = false;
        try {
            $resp = Http::timeout(3)->get("{$aiUrl}/demo");
            if ($resp->successful()) {
                $demoData = $resp->json();
                $aiOnline = true;
            }
        } catch (\Throwable) {}

        // Recent login attempts for live table
        $recentAttempts = LoginAttempt::with('user')->latest()->limit(10)->get();

        return view('admin.demo', compact('demoData', 'aiOnline', 'recentAttempts'));
    }

    public function aiDemoScore(Request $request)
    {
        $aiUrl = config('services.python_ai.url', 'http://127.0.0.1:5001');
        $payload = $request->validate([
            'hour_of_day'            => 'required|integer|min:0|max:23',
            'is_new_ip'              => 'required|integer|min:0|max:1',
            'is_new_device'          => 'required|integer|min:0|max:1',
            'failed_attempts'        => 'required|integer|min:0|max:20',
            'keystroke_speed_ms'     => 'required|numeric|min:0',
            'keystroke_irregularity' => 'required|numeric|min:0',
            'transaction_amount'     => 'required|numeric|min:0',
            'click_count_per_min'    => 'sometimes|numeric|min:0',
        ]);
        $payload['is_weekend'] = now()->isWeekend() ? 1 : 0;
        $payload['user_id']    = 0;
        try {
            $resp = Http::timeout(4)->post("{$aiUrl}/score", $payload);
            return response()->json($resp->json());
        } catch (\Throwable $e) {
            return response()->json(['error' => 'AI service unavailable: '.$e->getMessage()], 503);
        }
    }

    // ─── Coupons ──────────────────────────────────────────────────────────────

    public function coupons()
    {
        $coupons = Coupon::latest()->get();
        $couponByType   = [
            'Percentage' => Coupon::where('type','percentage')->count(),
            'Fixed'      => Coupon::where('type','fixed')->count(),
        ];
        $couponByStatus = [
            'Active'   => Coupon::where('is_active',true)->count(),
            'Inactive' => Coupon::where('is_active',false)->count(),
        ];
        $usagePerCoupon = Coupon::orderByDesc('used_count')->take(8)
            ->get()->map(fn($c)=>['code'=>$c->code,'used'=>(int)$c->used_count])->values()->toArray();
        return view('admin.coupons.index', compact('coupons','couponByType','couponByStatus','usagePerCoupon'));
    }

    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code'             => 'required|string|max:50|unique:coupons,code',
            'type'             => 'required|in:percentage,fixed',
            'value'            => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount'     => 'nullable|numeric|min:0',
            'usage_limit'      => 'nullable|integer|min:1',
            'expires_at'       => 'nullable|date|after:today',
            'is_active'        => 'nullable|boolean',
        ]);

        $validated['code']             = strtoupper($validated['code']);
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;
        $validated['is_active']        = $request->boolean('is_active', true);

        Coupon::create($validated);
        return redirect()->route('admin.coupons')->with('success', 'Coupon created successfully!');
    }

    public function updateCoupon(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code'             => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type'             => 'required|in:percentage,fixed',
            'value'            => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount'     => 'nullable|numeric|min:0',
            'usage_limit'      => 'nullable|integer|min:1',
            'expires_at'       => 'nullable|date',
            'is_active'        => 'nullable|boolean',
        ]);

        $validated['code']             = strtoupper($validated['code']);
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;
        $validated['is_active']        = $request->boolean('is_active', true);

        $coupon->update($validated);
        return redirect()->route('admin.coupons')->with('success', 'Coupon updated successfully!');
    }

    public function deleteCoupon(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('success', 'Coupon deleted.');
    }

    // ─── Returns / Refunds ────────────────────────────────────────────────────

    public function returns()
    {
        $returns = OrderReturn::with(['order', 'user'])->latest()->paginate(20);
        $stats = [
            'pending'   => OrderReturn::where('status', 'pending')->count(),
            'approved'  => OrderReturn::where('status', 'approved')->count(),
            'rejected'  => OrderReturn::where('status', 'rejected')->count(),
            'completed' => OrderReturn::where('status', 'completed')->count(),
        ];
        $returnsByStatus = [
            'Pending'   => $stats['pending'],
            'Approved'  => $stats['approved'],
            'Rejected'  => $stats['rejected'],
            'Completed' => $stats['completed'],
        ];
        $returnsByType = [
            'Refund'   => OrderReturn::where('return_type','refund')->count(),
            'Exchange' => OrderReturn::where('return_type','exchange')->count(),
        ];
        $returnsByDay = OrderReturn::where('created_at','>=',now()->subDays(6))
            ->selectRaw('DATE(created_at) as day, count(*) as cnt')
            ->groupBy('day')->orderBy('day')->get()
            ->map(fn($r)=>['day'=>$r->day,'cnt'=>(int)$r->cnt])->values()->toArray();
        return view('admin.returns.index', compact('returns','stats','returnsByStatus','returnsByType','returnsByDay'));
    }

    public function updateReturn(Request $request, OrderReturn $orderReturn)
    {
        $validated = $request->validate([
            'status'     => 'required|in:pending,approved,rejected,completed',
            'admin_note' => 'nullable|string|max:500',
        ]);
        $orderReturn->update($validated);
        return redirect()->route('admin.returns')->with('success', 'Return request status updated.');
    }

    // ─── Reviews ──────────────────────────────────────────────────────────────

    public function reviews(Request $request)
    {
        $query = Review::with(['product', 'user'])->latest();

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('user_name', 'like', '%' . $request->search . '%')
                  ->orWhere('comment', 'like', '%' . $request->search . '%');
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        $stats = [
            'total'   => Review::count(),
            'avg'     => round(Review::avg('rating'), 1),
            'five'    => Review::where('rating', 5)->count(),
            'four'    => Review::where('rating', 4)->count(),
            'three'   => Review::where('rating', 3)->count(),
            'two'     => Review::where('rating', 2)->count(),
            'one'     => Review::where('rating', 1)->count(),
        ];

        $products = Product::orderBy('name')->get(['id', 'name']);

        return view('admin.reviews.index', compact('reviews', 'stats', 'products'));
    }

    public function deleteReview(Review $review)
    {
        $productId = $review->product_id;
        $review->delete();

        // Recalculate product rating
        $product = Product::find($productId);
        if ($product) {
            $avg   = Review::where('product_id', $productId)->avg('rating') ?? 0;
            $count = Review::where('product_id', $productId)->count();
            $product->update(['rating' => round($avg, 1), 'reviews_count' => $count]);
        }

        return redirect()->route('admin.reviews')->with('success', 'Review deleted.');
    }

}

