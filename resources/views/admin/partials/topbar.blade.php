<header class="admin-topbar">
    {{-- Mobile toggle --}}
    <button class="tb-btn sb-toggle" id="sb-toggle" aria-label="Toggle menu">
        <i class="fas fa-bars"></i>
    </button>

    {{-- Page title --}}
    <div class="tb-title">
        <h1 id="tb-page-title">@yield('title', 'Dashboard')</h1>
    </div>

    {{-- Actions --}}
    <div class="tb-actions">
        <span class="tb-clock d-none d-md-flex align-items-center gap-1">
            <i class="far fa-clock" style="font-size:11px;opacity:.6"></i>
            <span id="tb-clock"></span>
        </span>

        <a href="{{ url('/') }}" target="_blank" class="tb-btn" title="View store">
            <i class="fas fa-external-link-alt"></i>
        </a>

        <div class="tb-user">
            <div class="av">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <span class="un d-none d-md-block">{{ auth()->user()->name ?? 'Admin' }}</span>
            <i class="fas fa-chevron-down" style="font-size:9px;color:var(--muted)"></i>
            <div class="tb-drop">
                <div class="tb-drop-inner">
                    <a href="{{ route('profile.show') }}"><i class="fas fa-user" style="width:14px"></i> Profile</a>
                    <a href="{{ url('/') }}" target="_blank"><i class="fas fa-store" style="width:14px"></i> View Store</a>
                    <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit()" class="del">
                        <i class="fas fa-sign-out-alt" style="width:14px"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>