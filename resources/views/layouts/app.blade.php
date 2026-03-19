<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TechStore') — Premium Tech Accessories</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter','Segoe UI',sans-serif; background:#f4f7fa; color:#1a1f2e; line-height:1.6; }

        :root {
            --green:   #00b894;
            --green-d: #009973;
            --green-l: #e6f7f4;
            --blue:    #0984e3;
            --danger:  #e84040;
            --warning: #f8b425;
            --text:    #1a1f2e;
            --text-m:  #64748b;
            --border:  #e8edf2;
            --bg:      #f4f7fa;
            --white:   #ffffff;
            --sh-s:    0 2px 8px rgba(0,0,0,.06);
            --sh-m:    0 6px 24px rgba(0,0,0,.10);
            --sh-l:    0 12px 40px rgba(0,0,0,.16);
            --r:  12px;
            --rs:  8px;
            --rxs: 6px;
            /* Bootstrap compat aliases */
            --primary: #0984e3;
            --secondary: #2563eb;
            --success: #00b894;
        }

        /* Bootstrap body reset override */
        body { background-color: #f4f7fa !important; color: #1a1f2e !important; }

        /* ── TOPBAR ── */
        .ts-topbar { background:linear-gradient(90deg,#0b1a2e,#152540); color:rgba(255,255,255,.62); font-size:12px; padding:7px 0; font-family:inherit; }
        .ts-topbar-in { max-width:1300px; margin:0 auto; padding:0 24px; display:flex; align-items:center; justify-content:space-between; }
        .ts-topbar a { color:rgba(255,255,255,.62); text-decoration:none; transition:color .2s; }
        .ts-topbar a:hover { color:var(--green); }
        .ts-tp-promo { font-weight:700; background:linear-gradient(90deg,var(--green),var(--blue)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }

        /* ── SITE NAVBAR ── */
        .ts-navbar { background:#fff; box-shadow:0 2px 16px rgba(0,0,0,.08); position:sticky; top:0; z-index:600; }
        .ts-nb-in { max-width:1300px; margin:0 auto; padding:10px 24px; display:flex; align-items:center; gap:16px; }
        .ts-logo { display:flex; align-items:center; gap:10px; text-decoration:none; flex-shrink:0; }
        .ts-logo img { height:42px; border-radius:8px; }
        .ts-logo-t { font-size:26px; font-weight:900; letter-spacing:-1px; }
        .ts-logo-t .t1 { color:var(--blue); font-style:italic; }
        .ts-logo-t .t2 { color:var(--text); }
        .ts-search { flex:1; display:flex; border:2px solid var(--border); border-radius:50px; overflow:hidden; transition:border-color .2s,box-shadow .2s; }
        .ts-search:focus-within { border-color:var(--green); box-shadow:0 0 0 3px rgba(0,184,148,.12); }
        .ts-search input { flex:1; padding:9px 20px; border:none; outline:none; font-size:14px; font-family:inherit; background:transparent; color:var(--text); }
        .ts-search button { padding:9px 22px; background:var(--green); border:none; color:white; cursor:pointer; font-size:14px; transition:background .2s; }
        .ts-search button:hover { background:var(--green-d); }
        .ts-actions { display:flex; align-items:center; gap:3px; flex-shrink:0; }
        .ts-btn { display:flex; flex-direction:column; align-items:center; gap:1px; padding:7px 10px; border-radius:var(--rs); text-decoration:none; color:var(--text-m); font-size:11px; font-weight:500; transition:all .2s; position:relative; white-space:nowrap; cursor:pointer; border:none; background:none; font-family:inherit; }
        .ts-btn i { font-size:18px; }
        .ts-btn:hover { background:var(--green-l); color:var(--green); transform:translateY(-1px); }
        .ts-btn.reg { background:linear-gradient(135deg,var(--green),var(--blue)); color:white; border-radius:50px; padding:8px 16px; flex-direction:row; gap:6px; }
        .ts-btn.reg:hover { opacity:.9; transform:translateY(-2px); box-shadow:0 4px 14px rgba(0,184,148,.4); }
        .ts-cart-bdg { position:absolute; top:3px; right:3px; background:var(--danger); color:white; border-radius:50%; width:16px; height:16px; font-size:9px; font-weight:700; display:flex; align-items:center; justify-content:center; }

        /* ── CAT NAV ── */
        .ts-catnav { background:#1a1f2e; }
        .ts-cn-in { max-width:1300px; margin:0 auto; padding:0 24px; display:flex; align-items:center; flex-wrap:nowrap; }
        .ts-ci { position:relative; }
        .ts-ci > a { display:flex; align-items:center; gap:6px; padding:11px 14px; color:rgba(255,255,255,.8); text-decoration:none; font-size:13px; font-weight:500; transition:all .2s; white-space:nowrap; }
        .ts-ci > a:hover, .ts-ci:hover > a { background:var(--green); color:white; }
        .ts-ci i.fa-chevron-down { font-size:9px; }
        .ts-drop { display:none; position:absolute; top:100%; left:0; background:white; border-radius:0 0 var(--rs) var(--rs); box-shadow:var(--sh-l); min-width:230px; z-index:999; border-top:3px solid var(--green); overflow:hidden; }
        .ts-ci:hover .ts-drop { display:block; animation:tsfd .18s ease; }
        @keyframes tsfd { from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:translateY(0);} }
        .ts-drop a { display:flex; align-items:center; gap:10px; padding:11px 18px; color:var(--text); text-decoration:none; font-size:13px; border-bottom:1px solid #f5f5f5; transition:all .2s; }
        .ts-drop a:last-child { border-bottom:none; }
        .ts-drop a i { width:16px; color:var(--green); }
        .ts-drop a:hover { background:var(--green-l); color:var(--green); padding-left:24px; }
        .ts-ci-ml { margin-left:auto; }

        /* ── PAGE BREADCRUMB BANNER ── */
        .ts-page-banner { background:linear-gradient(135deg,#0b1a2e,#1a3a5c); padding:28px 0; position:relative; overflow:hidden; }
        .ts-page-banner::before { content:''; position:absolute; width:400px; height:400px; background:radial-gradient(circle,rgba(0,184,148,.12) 0%,transparent 70%); top:-150px; right:-80px; pointer-events:none; }
        .ts-pb-in { max-width:1300px; margin:0 auto; padding:0 24px; position:relative; }
        .ts-pb-title { font-size:26px; font-weight:800; color:white; margin-bottom:6px; }
        .ts-breadcrumb { display:flex; align-items:center; gap:8px; font-size:12px; color:rgba(255,255,255,.5); }
        .ts-breadcrumb a { color:rgba(255,255,255,.5); text-decoration:none; transition:color .2s; }
        .ts-breadcrumb a:hover { color:var(--green); }
        .ts-breadcrumb i { font-size:9px; }

        /* ── MAIN CONTENT ── */
        .ts-main { min-height:62vh; padding:36px 0; }
        .ts-wrap { max-width:1300px; margin:0 auto; padding:0 24px; }

        /* ── FOOTER ── */
        .ts-footer { background:#0b1a2e; margin-top:48px; }
        .ts-ft-top { max-width:1300px; margin:0 auto; padding:48px 24px 36px; display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:44px; }
        .ts-f-logo { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
        .ts-f-logo img { height:36px; border-radius:6px; }
        .ts-f-logo-t { font-size:22px; font-weight:900; }
        .ts-f-logo-t .t1{color:var(--blue);font-style:italic;} .ts-f-logo-t .t2{color:white;}
        .ts-f-desc { font-size:13px; color:rgba(255,255,255,.5); line-height:1.8; margin-bottom:18px; }
        .ts-f-socials { display:flex; gap:10px; }
        .ts-f-soc { width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,.07); display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,.55); text-decoration:none; font-size:14px; transition:all .2s; }
        .ts-f-soc:hover { background:var(--green); color:white; transform:translateY(-2px); }
        .ts-f-hd { font-size:12px; font-weight:700; color:white; text-transform:uppercase; letter-spacing:.6px; margin-bottom:16px; }
        .ts-f-links { list-style:none; padding:0; margin:0; }
        .ts-f-links li { margin-bottom:10px; }
        .ts-f-links a { color:rgba(255,255,255,.5); text-decoration:none; font-size:13px; display:flex; align-items:center; gap:7px; transition:all .2s; }
        .ts-f-links a i { color:var(--green); font-size:11px; width:14px; }
        .ts-f-links a:hover { color:var(--green); padding-left:4px; }
        .ts-ft-bottom { border-top:1px solid rgba(255,255,255,.06); text-align:center; padding:20px 24px; font-size:12px; color:rgba(255,255,255,.28); }
        .ts-ft-bottom .fa-heart { color:#e84040; }

        /* ── NOTIFICATIONS (unchanged styling) ── */
        .cart-badge { position:absolute; top:3px; right:3px; background:var(--danger); color:white; border-radius:50%; width:16px; height:16px; font-size:9px; font-weight:700; display:flex; align-items:center; justify-content:center; }

        .chat-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex !important;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
            border: 2px solid white;
            animation: pulse-badge 2s infinite;
        }

        .chat-badge-navbar {
            display: inline-block !important;
            color: #f5576c;
            font-size: 12px;
            animation: pulse-dot 1.5s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }

        /* Main content keeps padding for Bootstrap inner pages */
        main { min-height:62vh; padding:0; }

        /* Scrollbar */
        ::-webkit-scrollbar { width:7px; }
        ::-webkit-scrollbar-track { background:#f1f1f1; }
        ::-webkit-scrollbar-thumb { background:var(--green); border-radius:4px; }
        ::-webkit-scrollbar-thumb:hover { background:var(--green-d); }

        /* Alert styling */
        .alert { border:none; border-radius:var(--rs); font-size:14px; }
        .alert-success { background:#d1fae5; color:#065f46; }
        .alert-danger  { background:#fee2e2; color:#991b1b; }
        .alert-info    { background:#dbeafe; color:#1e40af; }
        .btn-close { filter:none; }

        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border: none;
        }

        .btn-primary:hover {
            background: var(--secondary);
        }

        .btn-success {
            background: var(--success);
            border: none;
        }

        .btn-success:hover {
            background: #35a372;
        }

        /* Card */
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 8px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary);
        }

        /* ── HAMBURGER BUTTON (app layout) ── */
        .ts-hamburgerbtn {
            display:none; flex-direction:column; justify-content:center; align-items:center;
            width:40px; height:40px; border:none; background:transparent; cursor:pointer;
            border-radius:8px; gap:5px; flex-shrink:0;
        }
        .ts-hamburgerbtn span {
            display:block; width:22px; height:2.5px; background:var(--text);
            border-radius:2px; transition:all .3s;
        }
        .ts-hamburgerbtn.active span:nth-child(1){ transform:translateY(7.5px) rotate(45deg); }
        .ts-hamburgerbtn.active span:nth-child(2){ opacity:0; transform:scaleX(0); }
        .ts-hamburgerbtn.active span:nth-child(3){ transform:translateY(-7.5px) rotate(-45deg); }

        /* App mobile drawer */
        .ts-mdrawer { display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999; pointer-events:none; }
        .ts-mdrawer.open { display:block; pointer-events:all; }
        .ts-drawer-overlay { position:absolute; inset:0; background:rgba(0,0,0,.5); opacity:0; transition:opacity .3s; }
        .ts-mdrawer.open .ts-drawer-overlay { opacity:1; }
        .ts-drawer-panel {
            position:absolute; top:0; left:0; bottom:0; width:280px;
            background:white; box-shadow:4px 0 24px rgba(0,0,0,.2);
            transform:translateX(-100%); transition:transform .3s cubic-bezier(.4,0,.2,1);
            overflow-y:auto; display:flex; flex-direction:column;
        }
        .ts-mdrawer.open .ts-drawer-panel { transform:translateX(0); }
        .ts-drawer-hdr {
            background:linear-gradient(135deg,#0b1a2e,#152540);
            padding:20px 16px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;
        }
        .ts-drawer-logo { font-size:20px; font-weight:900; letter-spacing:-0.5px; }
        .ts-drawer-logo .t1{color:var(--blue);font-style:italic;} .ts-drawer-logo .t2{color:white;}
        .ts-drawer-cls {
            width:32px; height:32px; border:none; background:rgba(255,255,255,.12);
            color:white; border-radius:50%; cursor:pointer; font-size:16px;
            display:flex; align-items:center; justify-content:center;
        }
        .ts-drawer-nav { flex:1; padding:8px 0; }
        .ts-drawer-nav a {
            display:flex; align-items:center; gap:12px; padding:13px 20px;
            color:var(--text); text-decoration:none; font-size:14px; font-weight:500;
            border-bottom:1px solid var(--border); transition:all .2s;
        }
        .ts-drawer-nav a:hover { background:var(--green-l); color:var(--green); }
        .ts-drawer-nav a i { width:18px; color:var(--green); font-size:15px; }
        .ts-drawer-sub { padding:10px 20px 6px; font-size:11px; font-weight:700; color:var(--text-m); text-transform:uppercase; letter-spacing:.6px; background:var(--bg); }
        .ts-drawer-ft { padding:16px; border-top:1px solid var(--border); display:flex; gap:8px; flex-shrink:0; }
        .ts-drawer-ft a { flex:1; padding:10px; border-radius:8px; text-align:center; font-size:13px; font-weight:600; text-decoration:none; }
        .ts-dbtn-li { background:var(--green-l); color:var(--green); border:1.5px solid rgba(0,184,148,.3); }
        .ts-dbtn-su { background:var(--green); color:white; }

        /* ── RESPONSIVE ── */
        @media(max-width:1024px){
            .ts-ft-top { grid-template-columns:1fr 1fr; gap:32px; }
        }
        @media(max-width:768px){
            .ts-topbar { display:none; }
            .ts-nb-in { padding:8px 12px; gap:8px; flex-wrap:wrap; }
            .ts-search { order:3; flex:0 0 100%; border-radius:8px; }
            .ts-logo-t { font-size:20px; }
            .ts-btn span { display:none; }
            .ts-btn { padding:6px 8px; }
            .ts-btn i { font-size:17px; }
            .ts-btn.reg span { display:inline; }
            .ts-btn.reg { padding:7px 12px; font-size:12px; }
            .ts-catnav { display:none; }
            .ts-hamburgerbtn { display:flex; }
            .ts-ft-top { grid-template-columns:1fr 1fr; gap:20px; padding:28px 16px 20px; }
            .ts-pb-title { font-size:20px; }
            .ts-page-banner { padding:18px 0; }
            .ts-wrap { padding:0 12px; }
            #notif-drop { right:-60px !important; width:280px !important; }
        }
        @media(max-width:600px){
            .ts-btn.reg { display:none; }
        }
        @media(max-width:480px){
            .ts-ft-top { grid-template-columns:1fr; }
            .ts-ft-bottom { font-size:11px; padding:16px; }
            .ts-logo-t { font-size:17px; }
            .ts-logo img { height:34px; }
            .ts-pb-title { font-size:17px; }
        }
    </style>
</head>
<body>
    {{-- ══ TOPBAR ══ --}}
    <div class="ts-topbar">
        <div class="ts-topbar-in">
            <div style="display:flex;align-items:center;gap:20px">
                <span><i class="fas fa-phone-alt" style="color:var(--green)"></i> 0876-211-629</span>
                <span><i class="fas fa-envelope" style="color:var(--green)"></i> support@techstore.com</span>
            </div>
            <span class="ts-tp-promo"><i class="fas fa-bolt"></i> Free shipping on orders over $50</span>
            <div style="display:flex;gap:16px">
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact') }}">Contact</a>
            </div>
        </div>
    </div>

    {{-- ══ NAVBAR ══ --}}
    <nav class="ts-navbar">
        <div class="ts-nb-in">
            <a href="{{ route('home') }}" class="ts-logo">
                <img src="/images/logo.jpg" alt="TechStore">
                <span class="ts-logo-t"><span class="t1">Tech</span><span class="t2">Store</span></span>
            </a>
            <form class="ts-search" action="{{ route('products.index') }}" method="GET">
                <input type="text" name="search" placeholder="Search products..." value="{{ request('search','') }}">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div class="ts-actions">
                @auth
                    @php $authUser = Auth::user(); @endphp
                    @if($authUser->isAdmin())
                        <a href="{{ route('admin') }}" class="ts-btn" style="color:var(--danger)"><i class="fas fa-crown"></i><span>Admin</span></a>
                    @endif
                    <a href="{{ route('profile.show') }}" class="ts-btn">
                        @if($authUser->face_photo)
                            <img src="{{ $authUser->face_photo }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.5);vertical-align:middle;margin-right:4px">
                        @else
                            <i class="fas fa-user-circle"></i>
                        @endif
                        <span>{{ Str::limit($authUser->name,10) }}</span>
                    </a>
                    <a href="{{ route('wishlist') }}" class="ts-btn"><i class="fas fa-heart"></i><span>Wishlist</span></a>

                    {{-- Notification Bell --}}
                    <div style="position:relative" id="notif-wrap" onmouseleave="startNotifHideTimer()" onmouseenter="showNotifMenu()">
                        <button onclick="toggleNotifMenu()" onmouseenter="showNotifMenu()" id="notif-btn" class="ts-btn"><i class="fas fa-bell"></i><span>Notifications</span>
                            <span id="notif-badge" style="display:none;position:absolute;top:3px;right:3px;background:#ef4444;color:#fff;font-size:9px;font-weight:700;border-radius:9999px;min-width:16px;height:16px;line-height:16px;text-align:center;padding:0 3px">0</span>
                        </button>
                        <div id="notif-drop" style="display:none;position:absolute;top:calc(100% + 6px);right:0;width:320px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.15);z-index:9999;overflow:hidden">
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid #f1f5f9">
                                <span style="font-size:13px;font-weight:700;color:#1e293b">Notifications</span>
                                <button onclick="markAllRead()" style="font-size:11px;color:var(--green);background:none;border:none;cursor:pointer;padding:0;font-family:inherit">Mark all as read</button>
                            </div>
                            <div id="notif-list" style="max-height:320px;overflow-y:auto">
                                <div style="padding:30px;text-align:center;color:#94a3b8;font-size:13px" id="notif-empty">
                                    <i class="fas fa-bell-slash" style="font-size:24px;margin-bottom:8px;display:block"></i>No notifications
                                </div>
                            </div>
                            <div style="padding:10px 14px;border-top:1px solid #f1f5f9;text-align:center">
                                <a href="{{ route('notifications.index') }}" style="font-size:12px;color:var(--green);text-decoration:none"><i class="fas fa-list"></i> View all notifications</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('logout') }}" class="ts-btn" onclick="event.preventDefault();document.getElementById('lf-app').submit()"><i class="fas fa-sign-out-alt"></i><span>Sign Out</span></a>
                    <form id="lf-app" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
                @else
                    <a href="{{ route('login') }}" class="ts-btn"><i class="fas fa-sign-in-alt"></i><span>Sign In</span></a>
                    <a href="{{ route('register') }}" class="ts-btn reg"><i class="fas fa-user-plus"></i><span>Sign Up</span></a>
                @endauth
                <a href="{{ route('cart.index') }}" class="ts-btn" id="cart-nav-btn" style="position:relative">
                    <i class="fas fa-shopping-cart"></i><span>Cart</span>
                    <span class="ts-cart-bdg" id="cart-count" style="display:none">0</span>
                </a>
            </div>
            <button class="ts-hamburgerbtn" id="tsHamBtn">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    {{-- ══ CATEGORY NAV ══ --}}
    <div class="ts-catnav">
        <div class="ts-cn-in">
            <div class="ts-ci"><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></div>
            <div class="ts-ci"><a href="{{ route('about') }}"><i class="fas fa-info-circle"></i> About</a></div>
            <div class="ts-ci">
                <a href="#"><i class="fas fa-th-large"></i> Products <i class="fas fa-chevron-down"></i></a>
                <div class="ts-drop">
                    <a href="{{ route('products.index') }}?category=gaming"><i class="fas fa-gamepad"></i> Gaming</a>
                    <a href="{{ route('products.index') }}?category=peripherals"><i class="fas fa-mouse"></i> Peripherals</a>
                    <a href="{{ route('products.index') }}?category=storage"><i class="fas fa-hdd"></i> Storage &amp; Connectivity</a>
                    <a href="{{ route('products.index') }}?category=power"><i class="fas fa-plug"></i> Power &amp; Cooling</a>
                    <a href="{{ route('products.index') }}"><i class="fas fa-star" style="color:var(--green)"></i> <strong>All Products</strong></a>
                </div>
            </div>
            <div class="ts-ci">
                <a href="#"><i class="fas fa-headset"></i> Support <i class="fas fa-chevron-down"></i></a>
                <div class="ts-drop">
                    <a href="{{ route('contact') }}"><i class="fas fa-question-circle"></i> Help Center</a>
                    <a href="#" onclick="event.preventDefault();if(typeof openChat==='function')openChat()"><i class="fas fa-comments"></i> Chat with TechStore</a>
                </div>
            </div>
            <div class="ts-ci"><a href="{{ route('contact') }}"><i class="fas fa-envelope"></i> Contact</a></div>
            <div class="ts-ci"><a href="{{ route('news') }}"><i class="fas fa-newspaper"></i> News</a></div>
            @auth
            <div class="ts-ci ts-ci-ml"><a href="{{ route('orders.index') }}"><i class="fas fa-box-open"></i> My Orders</a></div>
            <div class="ts-ci"><a href="{{ route('compare.index') }}"><i class="fas fa-balance-scale"></i> Compare</a></div>
            @endauth
        </div>
    </div>

    {{-- ══ MOBILE DRAWER (app layout) ══ --}}
    <div class="ts-mdrawer" id="tsMDrawer">
        <div class="ts-drawer-overlay" id="tsDrawerOverlay"></div>
        <div class="ts-drawer-panel">
            <div class="ts-drawer-hdr">
                <span class="ts-drawer-logo"><span class="t1">Tech</span><span class="t2">Store</span></span>
                <button class="ts-drawer-cls" id="tsDrawerClose"><i class="fas fa-times"></i></button>
            </div>
            <nav class="ts-drawer-nav">
                <a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a>
                <a href="{{ route('about') }}"><i class="fas fa-info-circle"></i> About</a>
                <div class="ts-drawer-sub">Products</div>
                <a href="{{ route('products.index') }}?category=gaming"><i class="fas fa-gamepad"></i> Gaming</a>
                <a href="{{ route('products.index') }}?category=peripherals"><i class="fas fa-mouse"></i> Peripherals</a>
                <a href="{{ route('products.index') }}?category=storage"><i class="fas fa-hdd"></i> Storage &amp; Connectivity</a>
                <a href="{{ route('products.index') }}?category=power"><i class="fas fa-plug"></i> Power &amp; Cooling</a>
                <a href="{{ route('products.index') }}"><i class="fas fa-star"></i> All Products</a>
                <div class="ts-drawer-sub">Support</div>
                <a href="{{ route('contact') }}"><i class="fas fa-question-circle"></i> Help Center</a>
                <a href="#" onclick="event.preventDefault();if(typeof openChat==='function')openChat();tsMDrawerClose()"><i class="fas fa-comments"></i> Chat with TechStore</a>
                @auth
                <div class="ts-drawer-sub">My Account</div>
                <a href="{{ route('profile.show') }}"><i class="fas fa-user"></i> My Profile</a>
                <a href="{{ route('orders.index') }}"><i class="fas fa-box-open"></i> My Orders</a>
                <a href="{{ route('wishlist') }}"><i class="fas fa-heart"></i> Wishlist</a>
                <a href="{{ route('compare.index') }}"><i class="fas fa-balance-scale"></i> Compare</a>
                @endauth
                <a href="{{ route('news') }}"><i class="fas fa-newspaper"></i> News</a>
                <a href="{{ route('contact') }}"><i class="fas fa-envelope"></i> Contact</a>
            </nav>
            @guest
            <div class="ts-drawer-ft">
                <a href="{{ route('login') }}" class="ts-dbtn-li">Sign In</a>
                <a href="{{ route('register') }}" class="ts-dbtn-su">Sign Up</a>
            </div>
            @endguest
        </div>
    </div>

    {{-- ══ PAGE BANNER (yielded by child views or default) ══ --}}
    @hasSection('page_title')
    <div class="ts-page-banner">
        <div class="ts-pb-in">
            <div class="ts-pb-title">@yield('page_title')</div>
            <div class="ts-breadcrumb">
                <a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>@yield('page_title')</span>
            </div>
        </div>
    </div>
    @endif

    {{-- ══ OLD COMPAT STUB ══ --}}
    <div id="user-menu-wrap" style="display:none"><div id="user-nav-drop" style="display:none"></div></div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('security_warning'))
        @php $sw = session('security_warning'); @endphp
        <div class="alert alert-warning alert-dismissible fade show m-3" role="alert" style="border-left:4px solid #f59e0b">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>⚠️ Security Warning — Unusual Sign-In</strong>
            <p class="mb-0 mt-1" style="font-size:13px">AI system detected a sign-in attempt with <strong>{{ strtoupper($sw['level']) }}</strong>. If this wasn't you, please change your password immediately.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <strong>Error:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    {{-- ══════════ FOOTER ══════════ --}}
    <footer class="ts-footer">
        <div class="ts-ft-top">
            <div>
                <div class="ts-f-logo"><img src="/images/logo.jpg" alt="TechStore"><span class="ts-f-logo-t"><span class="t1">Tech</span><span class="t2">Store</span></span></div>
                <p class="ts-f-desc">TechStore — Leading premium tech accessories store. 100% genuine products, fast delivery, 24/7 support.</p>
                <div class="ts-f-socials">
                    <a href="#" class="ts-f-soc"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="ts-f-soc"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="ts-f-soc"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="ts-f-soc"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="ts-f-soc"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div>
                <div class="ts-f-hd">Customer Support</div>
                <ul class="ts-f-links">
                    <li><a href="#"><i class="fas fa-redo"></i> Return Policy</a></li>
                    <li><a href="#"><i class="fas fa-shield-alt"></i> Privacy Policy</a></li>
                    <li><a href="#"><i class="fas fa-credit-card"></i> Payment Guide</a></li>
                    <li><a href="#"><i class="fas fa-shopping-bag"></i> Shopping Guide</a></li>
                    <li><a href="#"><i class="fas fa-truck"></i> Shipping Policy</a></li>
                </ul>
            </div>
            <div>
                <div class="ts-f-hd">About TechStore</div>
                <ul class="ts-f-links">
                    <li><a href="{{ route('about') }}"><i class="fas fa-building"></i> About Us</a></li>
                    <li><a href="#"><i class="fas fa-users"></i> Careers</a></li>
                    <li><a href="{{ route('news') }}"><i class="fas fa-newspaper"></i> News</a></li>
                    <li><a href="{{ route('contact') }}"><i class="fas fa-envelope"></i> Contact</a></li>
                </ul>
            </div>
            <div>
                <div class="ts-f-hd">Contact</div>
                <ul class="ts-f-links">
                    <li><a href="#"><i class="fas fa-map-marker-alt"></i> Hem 1, Pham Ngu Lao St, Can Tho</a></li>
                    <li><a href="tel:19001234"><i class="fas fa-phone-alt"></i> 0876-211-629</a></li>
                    <li><a href="mailto:support@techstore.com"><i class="fas fa-envelope"></i> support@techstore.com</a></li>
                    <li><a href="#"><i class="fas fa-clock"></i> 8:00 AM – 10:00 PM daily</a></li>
                </ul>
            </div>
        </div>
        <div class="ts-ft-bottom">&copy; 2026 TechStore — All rights reserved. Built with <i class="fas fa-heart"></i> in Vietnam.</div>
    </footer>

    <!-- User menu toggle -->
    <script>
        function toggleUserMenu() {
            const drop = document.getElementById('user-nav-drop');
            if(drop) drop.style.display = drop.style.display === 'none' ? 'block' : 'none';
        }
        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('user-menu-wrap');
            if (wrap && !wrap.contains(e.target)) {
                const drop = document.getElementById('user-nav-drop');
                if (drop) drop.style.display = 'none';
            }
            const nw = document.getElementById('notif-wrap');
            if (nw && !nw.contains(e.target)) {
                const nd = document.getElementById('notif-drop');
                if (nd) nd.style.display = 'none';
            }
        });

        // ── Notification bell ─────────────────────────────────────────────
        @auth
        let notifLoaded = false;
        let notifHideTimer = null;
        function toggleNotifMenu() {
            const drop = document.getElementById('notif-drop');
            const isHidden = drop.style.display === 'none';
            drop.style.display = isHidden ? 'block' : 'none';
            if (isHidden && !notifLoaded) { loadNotifications(); }
        }
        function showNotifMenu() {
            cancelNotifHideTimer();
            const drop = document.getElementById('notif-drop');
            if (drop && drop.style.display === 'none') {
                drop.style.display = 'block';
                if (!notifLoaded) { loadNotifications(); }
            }
        }
        function startNotifHideTimer() {
            notifHideTimer = setTimeout(function() {
                const drop = document.getElementById('notif-drop');
                if (drop) drop.style.display = 'none';
            }, 300);
        }
        function cancelNotifHideTimer() {
            if (notifHideTimer) { clearTimeout(notifHideTimer); notifHideTimer = null; }
        }

        function loadNotifications() {
            fetch('{{ route("notifications.recent") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json()).then(data => {
                notifLoaded = true;
                updateNotifBadge(data.unreadCount);
                renderNotifications(data.notifications);
            }).catch(() => {});
        }

        function renderNotifications(list) {
            const el = document.getElementById('notif-list');
            const empty = document.getElementById('notif-empty');
            if (!list || list.length === 0) {
                if (empty) empty.style.display = 'block';
                return;
            }
            if (empty) empty.style.display = 'none';
            const sevColor = { info:'#3b82f6', success:'#10b981', warning:'#f59e0b', danger:'#ef4444', critical:'#7c3aed' };
            const sevIcon  = { info:'fa-info-circle', success:'fa-check-circle', warning:'fa-exclamation-triangle', danger:'fa-times-circle', critical:'fa-skull-crossbones' };
            let html = '';
            list.forEach(n => {
                const c = sevColor[n.severity] || '#3b82f6';
                const ico = sevIcon[n.severity] || 'fa-bell';
                const bg = n.read ? '#fff' : '#f0f9ff';
                html += `<div style="padding:10px 14px;border-bottom:1px solid #f8fafc;background:${bg};cursor:pointer"
                    onclick="readNotif(${n.id}, this, '${n.action_url || ''}')">
                    <div style="display:flex;gap:10px;align-items:flex-start">
                        <div style="width:28px;height:28px;background:${c}20;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px">
                            <i class="fas ${ico}" style="color:${c};font-size:12px"></i>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:12px;font-weight:${n.read ? '500' : '600'};color:#1e293b">${n.title}</div>
                            <div style="font-size:11px;color:#64748b;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${n.message}</div>
                            <div style="font-size:10px;color:#94a3b8;margin-top:3px">${n.time}</div>
                        </div>
                        ${!n.read ? '<div style="width:7px;height:7px;background:#3b82f6;border-radius:50%;flex-shrink:0;margin-top:5px"></div>' : ''}
                    </div>
                </div>`;
            });
            el.innerHTML = html;
        }

        function readNotif(id, el, url) {
            fetch('{{ url("/notifications") }}/' + id + '/read', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest' }
            }).then(() => {
                el.style.background = '#fff';
                const dot = el.querySelector('[style*="width:7px"]');
                if (dot) dot.remove();
                notifLoaded = false;
                const badge = document.getElementById('notif-badge');
                if (badge) {
                    const cnt = Math.max(0, parseInt(badge.textContent || '0') - 1);
                    badge.textContent = cnt;
                    badge.style.display = cnt > 0 ? 'block' : 'none';
                }
            });
            if (url) { window.location.href = url; }
        }

        function markAllRead() {
            fetch('{{ route("notifications.read-all") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest' }
            }).then(() => {
                notifLoaded = false;
                updateNotifBadge(0);
                document.querySelectorAll('#notif-list > div').forEach(d => { d.style.background = '#fff'; });
                document.querySelectorAll('#notif-list [style*="width:7px"]').forEach(d => d.remove());
            });
        }

        function updateNotifBadge(count) {
            const badge = document.getElementById('notif-badge');
            if (!badge) return;
            if (count > 0) { badge.textContent = count > 99 ? '99+' : count; badge.style.display = 'block'; }
            else { badge.style.display = 'none'; }
        }

        // Poll every 60s for unread count
        function pollNotifCount() {
            fetch('{{ route("notifications.recent") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json()).then(d => { updateNotifBadge(d.unreadCount); notifLoaded = false; }).catch(() => {});
        }
        pollNotifCount();
        setInterval(pollNotifCount, 60000);
        @endauth
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Cart Badge Script -->
    <script>
        function updateCartCount() {
            fetch('/cart', { headers: {'X-Requested-With':'XMLHttpRequest'} })
                .then(r => r.text()).then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const src = doc.querySelector('#cart-count');
                    const badge = document.getElementById('cart-count');
                    if (src && badge) {
                        const n = parseInt(src.textContent) || 0;
                        badge.textContent = n;
                        badge.style.display = n > 0 ? 'flex' : 'none';
                    }
                }).catch(() => {});
        }
        document.addEventListener('DOMContentLoaded', updateCartCount);
        document.addEventListener('cartUpdated', updateCartCount);
    </script>

    @yield('scripts')

    <!-- Mobile Drawer JS -->
    <script>
        function tsMDrawerOpen(){
            document.getElementById('tsMDrawer').classList.add('open');
            document.getElementById('tsHamBtn').classList.add('active');
            document.body.style.overflow='hidden';
        }
        function tsMDrawerClose(){
            document.getElementById('tsMDrawer').classList.remove('open');
            document.getElementById('tsHamBtn').classList.remove('active');
            document.body.style.overflow='';
        }
        (function(){
            var btn=document.getElementById('tsHamBtn');
            var cls=document.getElementById('tsDrawerClose');
            var ov=document.getElementById('tsDrawerOverlay');
            if(btn) btn.addEventListener('click',tsMDrawerOpen);
            if(cls) cls.addEventListener('click',tsMDrawerClose);
            if(ov)  ov.addEventListener('click',tsMDrawerClose);
            document.addEventListener('keydown',function(e){ if(e.key==='Escape') tsMDrawerClose(); });
        })();
    </script>

    <!-- Chatbot Widget -->
    @include('components.chatbot-widget')
</body>
</html>
