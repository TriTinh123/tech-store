<aside class="admin-sidebar">
    {{-- Brand --}}
    <a href="{{ route('admin') }}" class="sb-brand">
        <div class="bi"><i class="fas fa-bolt"></i></div>
        <span class="bn">TechStore</span>
        <span class="bp">Admin</span>
    </a>

    {{-- Nav --}}
    <nav class="sb-nav">

        <div class="sb-group">
            <a href="{{ route('admin') }}"
               class="nav-item {{ request()->routeIs('admin') ? 'active' : '' }}"
               data-nav="/admin" data-nav-exact="1">
                <i class="fas fa-home ni"></i><span>Dashboard</span>
            </a>
        </div>

        <div class="sb-group">
            <div class="sb-label">Sales</div>
            <a href="{{ route('admin.orders') }}"
               class="nav-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}"
               data-nav="/admin/orders">
                <i class="fas fa-shopping-bag ni"></i><span>Order</span>
            </a>
        </div>

        <div class="sb-group">
            <div class="sb-label">Catalog</div>
            <a href="{{ route('admin.products') }}"
               class="nav-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}"
               data-nav="/admin/products">
                <i class="fas fa-box ni"></i><span>Products</span>
            </a>
            <a href="{{ route('admin.categories') }}"
               class="nav-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}"
               data-nav="/admin/categories">
                <i class="fas fa-tags ni"></i><span>Categories</span>
            </a>
        </div>

        <div class="sb-group">
            <div class="sb-label">Management</div>
            <a href="{{ route('admin.users') }}"
               class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
               data-nav="/admin/users">
                <i class="fas fa-users ni"></i><span>Users</span>
            </a>
            <a href="{{ route('admin.coupons') }}"
               class="nav-item {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}"
               data-nav="/admin/coupons">
                <i class="fas fa-ticket-alt ni"></i><span>Coupon code</span>
            </a>
            <a href="{{ route('admin.returns') }}"
               class="nav-item {{ request()->routeIs('admin.returns*') ? 'active' : '' }}"
               data-nav="/admin/returns">
                <i class="fas fa-undo-alt ni"></i><span>Returns / Refunds</span>
                @php $pendingReturns = \App\Models\OrderReturn::where('status','pending')->count(); @endphp
                @if($pendingReturns > 0)
                <span style="background:#ef4444;color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;margin-left:auto">{{ $pendingReturns }}</span>
                @endif
            </a>
            <a href="{{ route('admin.reviews') }}"
               class="nav-item {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}"
               data-nav="/admin/reviews">
                <i class="fas fa-star ni"></i><span>Rating products</span>
                @php $totalReviews = \App\Models\Review::count(); @endphp
                @if($totalReviews > 0)
                <span style="background:#f59e0b;color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;margin-left:auto">{{ $totalReviews }}</span>
                @endif
            </a>
        </div>

        <div class="sb-group">
            <div class="sb-label">System</div>
            <a href="{{ route('admin.security') }}"
               class="nav-item {{ request()->routeIs('admin.security') ? 'active' : '' }}"
               data-nav="/admin/security"
               style="margin:4px 10px 2px;border-radius:8px;border:1px solid rgba(239,68,68,.4);background:rgba(239,68,68,.12);color:#fca5a5;padding:9px 12px;transition:all .2s"
               onmouseenter="this.style.background='rgba(239,68,68,.22)';this.style.color='#fff'"
               onmouseleave="this.style.background='rgba(239,68,68,.12)';this.style.color='#fca5a5'">
                <i class="fas fa-shield-alt ni" style="color:#f87171"></i><span style="font-weight:600">Security Log</span>
            </a>
            <a href="{{ route('admin.demo') }}"
               class="nav-item {{ request()->routeIs('admin.demo*') ? 'active' : '' }}"
               data-nav="/admin/demo"
               style="margin:4px 10px 6px;border-radius:8px;border:1px solid rgba(99,102,241,.45);background:rgba(99,102,241,.14);color:#a5b4fc;padding:9px 12px;transition:all .2s"
               onmouseenter="this.style.background='rgba(99,102,241,.28)';this.style.color='#fff'"
               onmouseleave="this.style.background='rgba(99,102,241,.14)';this.style.color='#a5b4fc'">
                <i class="fas fa-brain ni" style="color:#818cf8"></i><span style="font-weight:600">AI Demo</span>
            </a>
        </div>

    </nav>

    {{-- Bottom user --}}
    <div class="sb-bottom">
        <div class="sb-user">
            <div class="av">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div class="ui">
                <div class="un">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="ur">Administrator</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin:0">
                @csrf
                <button type="submit" class="sb-out" title="Sign Out">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>