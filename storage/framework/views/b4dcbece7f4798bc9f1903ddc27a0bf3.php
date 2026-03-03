
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Mua sắm điện tử online</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        /* Header Top */
        .header-top {
            background: #00b894;
            color: white;
            padding: 8px 0;
            font-size: 12px;
        }

        .header-top-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-top-left {
            display: flex;
            gap: 20px;
        }

        .header-top-right {
            display: flex;
            gap: 20px;
        }

        /* Navigation */
        .navbar {
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .logo span {
            color: #00b894;
        }

        .search-container {
            flex: 1;
            margin: 0 30px;
            display: flex;
        }

        .search-container input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 3px 0 0 3px;
            outline: none;
        }

        .search-container button {
            padding: 10px 20px;
            background: #00b894;
            color: white;
            border: none;
            border-radius: 0 3px 3px 0;
            cursor: pointer;
            font-weight: 600;
        }

        .search-container button:hover {
            background: #00a080;
        }

        .nav-icons {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-icons a {
            color: #333;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 12px;
        }

        .nav-icons i {
            font-size: 20px;
            margin-bottom: 5px;
        }

        /* Category Menu */
        .category-menu {
            background: white;
            border-top: 1px solid #ddd;
        }

        .category-menu-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            gap: 40px;
        }

        .category-menu-item {
            position: relative;
            display: inline-block;
        }

        .category-menu a {
            padding: 15px 0;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .category-menu a:hover {
            color: #00b894;
            border-bottom-color: #00b894;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: 3px solid #00b894;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            min-width: 200px;
            z-index: 1000;
            list-style: none;
            margin-top: -1px;
        }

        .category-menu-item:hover .dropdown-menu {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        .dropdown-menu li {
            border-bottom: 1px solid #f0f0f0;
        }

        .dropdown-menu li:last-child {
            border-bottom: none;
        }

        .dropdown-menu a {
            display: block;
            padding: 12px 20px;
            color: #333;
            font-size: 13px;
            font-weight: 400;
            border-bottom: none;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }

        .dropdown-menu a:hover {
            background: #f5f5f5;
            color: #00b894;
            border-left-color: #00b894;
            padding-left: 25px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-toggle::after {
            content: '▼';
            font-size: 10px;
            margin-left: 5px;
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 15px;
        }

        /* Sidebar */
        .sidebar {
            grid-column: 1;
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .sidebar-section {
            background: white;
            border-radius: 5px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .sidebar-title {
            background: #00b894;
            color: white;
            padding: 12px 15px;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-title i {
            font-size: 14px;
        }

        .sidebar-list {
            list-style: none;
        }

        .sidebar-list li {
            border-bottom: 1px solid #f0f0f0;
        }

        .sidebar-list li:last-child {
            border-bottom: none;
        }

        .sidebar-list a {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            font-size: 12px;
            transition: all 0.3s;
        }

        .sidebar-list a:hover {
            background: #f9f9f9;
            color: #00b894;
            padding-left: 20px;
        }

        .sidebar-list a.active {
            background: #e8f8f5;
            color: #00b894;
            font-weight: 600;
            border-left: 3px solid #00b894;
            padding-left: 12px;
        }

        .sidebar-list i {
            margin-right: 8px;
            color: #00b894;
            font-size: 12px;
        }

        .hot-deal-item {
            text-align: center;
        }

        /* Main Content */
        .main-content {
            grid-column: 2;
        }
        .banner-section {
            margin-bottom: 30px;
        }

        .banner {
            display: flex;
            gap: 20px;
            margin-bottom: 0;
        }

        .banner-main {
            flex: 2;
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
            border-radius: 8px;
            overflow: hidden;
            height: 350px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 40px;
            color: #333;
        }

        .banner-main-text h2 {
            font-size: 48px;
            margin-bottom: 10px;
            color: #ff6b6b;
            font-weight: 900;
            letter-spacing: -2px;
        }

        .banner-main-text p {
            font-size: 16px;
            margin-bottom: 25px;
            opacity: 0.8;
            color: #ff6b6b;
            font-weight: 600;
        }

        .banner-main-text button {
            padding: 14px 40px;
            background: white;
            color: #ff6b6b;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .banner-main-text button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }

        .banner-side {
            flex: 1;
            display: none;
        }

        .banner-item {
            background: white;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .banner-item:hover {
            transform: translateY(-5px);
        }

        .banner-item h3 {
            color: #00b894;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .banner-item p {
            font-size: 12px;
            color: #666;
        }

        /* Features */
        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .feature-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .feature-card i {
            font-size: 32px;
            color: #00b894;
            margin-bottom: 10px;
        }

        .feature-card h3 {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .feature-card p {
            font-size: 12px;
            color: #666;
        }

        /* Section Title */
        .section-title {
            background: white;
            padding: 30px 20px;
            margin: 40px 0 20px 0;
            border-left: 4px solid #00b894;
            border-radius: 3px;
            text-align: center;
        }

        .section-title h2 {
            font-size: 26px;
            margin-bottom: 5px;
            color: #333;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: #00b894;
        }

        .section-title p {
            font-size: 12px;
            color: #666;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .product-card:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            transform: translateY(-8px);
        }

        .product-image {
            position: relative;
            width: 100%;
            height: 200px;
            background: #f5f5f5;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff6b6b;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }

        .hot-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #00b894;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
        }

        .product-info {
            padding: 15px;
        }

        .product-name {
            font-size: 13px;
            margin-bottom: 8px;
            line-height: 1.4;
            min-height: 32px;
        }

        .product-name a {
            text-decoration: none;
            color: #333;
        }

        .product-name a:hover {
            color: #00b894;
        }

        .product-rating {
            font-size: 12px;
            color: #ffa500;
            margin-bottom: 8px;
        }

        .product-price {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }

        .product-price-current {
            font-size: 18px;
            font-weight: 900;
            color: #ff6b6b;
        }

        .product-price-original {
            font-size: 12px;
            text-decoration: line-through;
            color: #ccc;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .product-actions button {
            flex: 1;
            padding: 8px;
            background: #00b894;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .product-actions button:hover {
            background: #00a080;
        }

        /* Animation Flying Cart */
        @keyframes flyToCart {
            0% {
                opacity: 1;
                transform: translate(0, 0) scale(1) rotate(0deg);
                z-index: 1000;
                filter: blur(0px) drop-shadow(0 0 0px rgba(0, 184, 148, 0));
            }
            30% {
                filter: blur(0px) drop-shadow(0 10px 25px rgba(0, 184, 148, 0.5));
                transform: translate(calc(var(--tx) * 0.3), calc(var(--ty) * 0.3)) scale(1.1) rotate(90deg);
            }
            70% {
                filter: blur(1px) drop-shadow(0 10px 25px rgba(0, 184, 148, 0.3));
                transform: translate(calc(var(--tx) * 0.7), calc(var(--ty) * 0.7)) scale(0.6) rotate(180deg);
            }
            100% {
                opacity: 0;
                transform: translate(var(--tx), var(--ty)) scale(0.1) rotate(360deg);
                z-index: 1000;
                filter: blur(2px) drop-shadow(0 0 0px rgba(0, 184, 148, 0));
            }
        }

        @keyframes particleFloat {
            0% {
                opacity: 1;
                transform: translate(0, 0) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(var(--px), var(--py)) scale(0);
            }
        }

        .flying-item {
            position: fixed;
            animation: flyToCart 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
            pointer-events: none;
            background: linear-gradient(135deg, #00b894 0%, #009973 100%);
            padding: 12px 15px;
            border-radius: 50%;
            color: white;
            font-size: 18px;
            box-shadow: 0 8px 20px rgba(0, 184, 148, 0.4), inset -2px -2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .flying-item::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            animation: pulse 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 184, 148, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(0, 184, 148, 0.3);
            }
            100% {
                box-shadow: 0 0 0 20px rgba(0, 184, 148, 0);
            }
        }

        .particle {
            position: fixed;
            pointer-events: none;
            border-radius: 50%;
            background: #00b894;
            animation: particleFloat 0.8s ease-out forwards;
        }

        /* Notification animations */
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        /* Brands */
        .brands {
            display: flex;
            justify-content: space-around;
            align-items: center;
            background: white;
            padding: 30px 20px;
            margin-bottom: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .brands img {
            max-height: 50px;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .brands img:hover {
            opacity: 1;
        }

        /* Advertisement Banner */
        .ad-banner {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            border-radius: 8px;
            margin: 40px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 30px 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            color: white;
        }

        .ad-banner h3 {
            font-size: 36px;
            font-weight: 900;
            text-align: center;
            flex: 1;
            color: #333;
            line-height: 1.2;
        }

        /* Device-specific layouts */
        body.device-mobile .container {
            grid-template-columns: 1fr;
        }

        body.device-mobile .sidebar {
            display: none;
        }

        body.device-mobile .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        body.device-tablet .products-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        body.device-desktop .products-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .ad-banner-img {
            width: 150px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ad-banner-img i {
            font-size: 80px;
            opacity: 0.8;
        }

        /* Footer */
        footer {
            background: #333;
            color: white;
            padding: 40px 0 20px;
            margin-top: 40px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 5px auto 15px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }

        .footer-section h3 {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .footer-section a,
        .footer-section p {
            display: block;
            font-size: 12px;
            margin-bottom: 8px;
            color: #aaa;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid #555;
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #aaa;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                grid-column: 1;
                margin-bottom: 20px;
                position: static;
            }

            .main-content {
                grid-column: 1;
            }

            .banner {
                flex-direction: column;
            }

            .features {
                grid-template-columns: 1fr;
            }

            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .category-menu-content {
                gap: 15px;
            }

            .footer-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: 1fr;
            }

            .navbar-content {
                flex-direction: column;
                gap: 10px;
            }

            .search-container {
                margin: 0;
                width: 100%;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }

            .ad-banner {
                flex-direction: column;
                height: auto;
                text-align: center;
            }

            .ad-banner h3 {
                font-size: 18px;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Top -->
    <div class="header-top">
        <div class="header-top-content">
            <div style="flex: 1;">
                <a href="#" style="color: white; text-decoration: none;">← Quay về xem chi tiết</a>
            </div>
            <div style="text-align: center; flex: 1;">
                <a href="#" onclick="switchDevice('desktop', event)" style="color: white; text-decoration: none; margin: 0 20px; padding: 5px 10px; background: rgba(255,255,255,0.2); border-radius: 3px;">🖥️ Desktop</a>
                <a href="#" onclick="switchDevice('mobile', event)" style="color: white; text-decoration: none; margin: 0 20px; padding: 5px 10px; background: rgba(255,255,255,0.2); border-radius: 3px;">📱 Mobile</a>
                <a href="#" onclick="switchDevice('tablet', event)" style="color: white; text-decoration: none; margin: 0 20px; padding: 5px 10px; background: rgba(255,255,255,0.2); border-radius: 3px;">📱 Tablet</a>
            </div>
            <div style="text-align: right; flex: 1;">
                <a href="#" style="color: white; text-decoration: none;">Chọn giao diện này</a>
            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-content">
            <div class="logo" style="font-size: 24px; white-space: nowrap; display: flex; align-items: center; gap: 10px;">
                <img src="/Image/logo.jpg" alt="TechStore Logo" style="height: 40px; width: auto; border-radius: 4px;">
                <span style="font-weight: 900; letter-spacing: -1px; font-size: 28px;"><span style="color: #1E90FF; font-style: italic;">Tech</span><span style="color: white; text-shadow: -1px 0 #1E90FF, 1px 0 #1E90FF, 0 1px #1E90FF;">Store</span></span>
            </div>
            <form class="search-container" style="margin: 0 30px; flex: 1;" action="<?php echo e(route('products.index')); ?>" method="GET">
                <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?php echo e(request('search', '')); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div class="nav-icons">
                <a href="<?php echo e(route('address')); ?>">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Địa chỉ</span>
                </a>
                <?php if(auth()->guard()->check()): ?>
                    <?php
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                    ?>
                    <a href="#" style="font-size: 12px;">
                        <i class="fas fa-user"></i>
                        <span><?php echo e($user->name); ?></span>
                    </a>
                    <a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;"><?php echo csrf_field(); ?></form>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Đăng nhập</span>
                    </a>
                    <a href="<?php echo e(route('register')); ?>">
                        <i class="fas fa-user-plus"></i>
                        <span>Đăng kí</span>
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('cart.index')); ?>" style="position: relative;">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Giỏ hàng</span>
                    <?php
                        $cartItems = session()->get('cart', []);
                        $cartCount = array_sum($cartItems);
                    ?>
                    <?php if($cartCount > 0): ?>
                        <span style="position: absolute; top: -5px; right: -5px; background: #ff6b6b; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;"><?php echo e($cartCount); ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </nav>

    <!-- Category Menu -->
    <div class="category-menu">
        <div class="category-menu-content">
            <a href="<?php echo e(route('home')); ?>">TRANG CHỦ</a>
            <a href="<?php echo e(route('about')); ?>">GIỚI THIỆU</a>
            <div class="category-menu-item">
                <a href="#" class="dropdown-toggle">SẢN PHẨM</a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo e(route('products.index')); ?>?category=peripherals"><i class="fas fa-mouse"></i> Phụ kiện ngoại vi</a></li>
                    <li><a href="<?php echo e(route('products.index')); ?>?category=storage"><i class="fas fa-hdd"></i> Lưu trữ & kết nối</a></li>
                    <li><a href="<?php echo e(route('products.index')); ?>?category=power"><i class="fas fa-plug"></i> Nguồn & làm mát</a></li>
                    <li><a href="<?php echo e(route('products.index')); ?>?category=protection"><i class="fas fa-shield-alt"></i> Bảo vệ & trang trí</a></li>
                    <li><a href="<?php echo e(route('products.index')); ?>?category=gaming"><i class="fas fa-gamepad"></i> Gaming</a></li>
                    <li><a href="<?php echo e(route('products.index')); ?>?category=security"><i class="fas fa-lock"></i> Bảo mật</a></li>
                    <li><a href="<?php echo e(route('products.index')); ?>?category=office"><i class="fas fa-briefcase"></i> Văn phòng</a></li>
                    <li style="border-top: 1px solid #ddd; margin-top: 8px; padding-top: 8px;"><a href="<?php echo e(route('products.index')); ?>" style="font-weight: 600; color: #00b894;"><i class="fas fa-star"></i> Xem tất cả sản phẩm</a></li>
                </ul>
            </div>
            <div class="category-menu-item">
                <a href="#" class="dropdown-toggle">HỖ TRỢ</a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo e(route('contact')); ?>"><i class="fas fa-question-circle"></i> Trung tâm trợ giúp</a></li>
                    <li><a href="#" onclick="event.preventDefault(); toggleChatbot();"><i class="fas fa-comments"></i> Trò chuyện với TechStore</a></li>
                </ul>
            </div>
            <a href="<?php echo e(route('contact')); ?>">LIÊN HỆ</a>
            <a href="<?php echo e(route('news')); ?>">TIN TỨC</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-section">
                <div class="sidebar-title">
                    <i class="fas fa-bars"></i> DANH MỤC
                </div>
                <ul class="sidebar-list">
                    <li><a href="/?category=peripherals" class="<?php if(request('category') == 'peripherals'): ?> active <?php endif; ?>"><i class="fas fa-mouse"></i> Phụ kiện ngoại vi</a></li>
                    <li><a href="/?category=storage" class="<?php if(request('category') == 'storage'): ?> active <?php endif; ?>"><i class="fas fa-hdd"></i> Lưu trữ & kết nối</a></li>
                    <li><a href="/?category=power" class="<?php if(request('category') == 'power'): ?> active <?php endif; ?>"><i class="fas fa-plug"></i> Nguồn & làm mát</a></li>
                    <li><a href="/?category=protection" class="<?php if(request('category') == 'protection'): ?> active <?php endif; ?>"><i class="fas fa-shield-alt"></i> Bảo vệ & trang trí</a></li>
                    <li><a href="/?category=gaming" class="<?php if(request('category') == 'gaming'): ?> active <?php endif; ?>"><i class="fas fa-gamepad"></i> Gaming</a></li>
                    <li><a href="/?category=security" class="<?php if(request('category') == 'security'): ?> active <?php endif; ?>"><i class="fas fa-lock"></i> Bảo mật</a></li>
                    <li><a href="/?category=office" class="<?php if(request('category') == 'office'): ?> active <?php endif; ?>"><i class="fas fa-briefcase"></i> Văn phòng</a></li>
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-title">
                    <i class="fas fa-history"></i> SẢN PHẨM CŨ
                </div>
                <ul class="sidebar-list">
                    <li><a href="#">--</a></li>
                    <li><a href="#">--</a></li>
                    <li><a href="#">--</a></li>
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-title">
                    <i class="fas fa-filter"></i> THẺ SẢN PHẨM
                </div>
                <ul class="sidebar-list">
                    <li><a href="#"><i class="fas fa-tag"></i> HOÀN TIỀN</a></li>
                    <li><a href="#"><i class="fas fa-truck"></i> VẬN CHUYỂN</a></li>
                    <li><a href="#"><i class="fas fa-gift"></i> KHUYẾN MÃI</a></li>
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-title">
                    <i class="fas fa-fire"></i> UU ĐÃI ĐẶC BIỆT
                </div>
                <ul class="sidebar-list">
                    <li><a href="#">🖱️ Chuột Gaming</a></li>
                    <li><a href="#">⌨️ Bàn phím Cơ</a></li>
                    <li><a href="#">🎧 Tai nghe Cao cấp</a></li>
                    <li><a href="#">🖥️ Màn hình 4K</a></li>
                    <li><a href="#">💾 SSD Ngoài</a></li>
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-title">
                    <i class="fas fa-envelope"></i> BẢN TIN
                </div>
                <div style="padding: 15px;">
                    <p style="font-size: 12px; margin-bottom: 10px;">Nhận thông tin mới nhất từ shop</p>
                    <input type="email" placeholder="Email của bạn" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px; margin-bottom: 8px;">
                    <button style="width: 100%; padding: 8px; background: #00b894; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; font-weight: 600;">Đăng kí</button>
                </div>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-title">
                    <i class="fas fa-bolt"></i> HOT DEALS
                </div>
                <div style="padding: 15px;">
                    <div class="hot-deal-item">
                        <div style="width: 100%; height: 120px; background: #f5f5f5; border-radius: 3px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; color: #999;">
                            <i class="fas fa-image" style="font-size: 32px;"></i>
                        </div>
                        <h4 style="font-size: 13px; margin-bottom: 5px;">Sản phẩm bán chạy</h4>
                        <p style="font-size: 11px; color: #00b894; margin-bottom: 8px;">12.580.000₫</p>
                        <button style="width: 100%; padding: 8px; background: #00b894; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; font-weight: 600;">Xem chi tiết</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
        <!-- Banner Section -->
        <div class="banner-section">
            <div class="banner">
                <div class="banner-main">
                    <div class="banner-main-text">
                        <h2>PHỤ KIỆN CÔNG NGHỆ<br>CHẤT LƯỢNG CAO - GIÁ TỐT</h2>
                        <button>MUA NGAY</button>
                    </div>
                </div>
                <div class="banner-side">
                    <div class="banner-item">
                        <h3>✓ HOÀN TIỀN</h3>
                        <p>Nếu không hài lòng</p>
                    </div>
                    <div class="banner-item">
                        <h3>✓ VẬN CHUYỂN</h3>
                        <p>Toàn quốc miễn phí</p>
                    </div>
                    <div class="banner-item">
                        <h3>✓ KHUYẾN MÃI</h3>
                        <p>Cập nhật hàng tuần</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="features">
            <div class="feature-card">
                <i class="fas fa-truck"></i>
                <h3>Giao hàng nhanh</h3>
                <p>Giao hàng trong vòng 24 giờ</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Bảo hành chính hãng</h3>
                <p>Bảo hành đầy đủ theo luật</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-undo"></i>
                <h3>Hoàn đổi dễ dàng</h3>
                <p>30 ngày hoàn đổi miễn phí</p>
            </div>
        </div>

        <!-- Danh Học Section -->
        <div class="section-title">
            <h2>SẢN PHẨM MỚI</h2>
        </div>

        <div class="products-grid">
            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if($product->discount_percentage > 0): ?>
                            <span class="discount-badge">-<?php echo e($product->discount_percentage); ?>%</span>
                        <?php endif; ?>
                        <span class="hot-badge">HOT</span>
                        <img src="<?php echo e($product->image ?? 'https://via.placeholder.com/200x200?text=' . urlencode($product->name)); ?><?php echo e($product->image ? '?v=' . md5($product->updated_at) : ''); ?>" alt="<?php echo e($product->name); ?>">
                    </div>
                    <div class="product-info">
                        <div class="product-name">
                            <a href="<?php echo e(route('product.show', $product->id)); ?>"><?php echo e($product->name); ?></a>
                        </div>
                        <?php if($product->rating): ?>
                            <div class="product-rating">
                                <?php for($i = 0; $i < 5; $i++): ?>
                                    <?php if($i < $product->rating): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                (<?php echo e($product->reviews_count ?? 0); ?>)
                            </div>
                        <?php endif; ?>
                        <div class="product-price">
                            <span class="product-price-current"><?php echo e(number_format($product->price, 0, ',', '.')); ?>₫</span>
                            <?php if($product->original_price): ?>
                                <span class="product-price-original"><?php echo e(number_format($product->original_price, 0, ',', '.')); ?>₫</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions" style="display: flex; gap: 8px;">
                            <a href="<?php echo e(route('product.show', $product->id)); ?>" style="flex: 1; display: flex; align-items: center; justify-content: center; background: #00b894; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; font-weight: 600; text-decoration: none; transition: background 0.3s;">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                            <form action="<?php echo e(route('cart.add', $product->id)); ?>" method="POST" style="flex: 1;" class="add-to-cart-form">
                                <?php echo csrf_field(); ?>
                                <button type="submit" style="width: 100%; padding: 8px; background: #ff6b6b; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; font-weight: 600; transition: background 0.3s;">
                                    <i class="fas fa-shopping-cart"></i> Giỏ hàng
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p style="grid-column: 1/-1; text-align: center; padding: 40px;">Chưa có sản phẩm nào</p>
            <?php endif; ?>
        </div>

        <!-- View All Button -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="<?php echo e(route('products.index')); ?>" style="display: inline-block; padding: 12px 40px; background: #333; color: white; border: none; border-radius: 3px; cursor: pointer; font-weight: 600; font-size: 14px; text-decoration: none; transition: background 0.3s;" onmouseover="this.style.background='#555';" onmouseout="this.style.background='#333';">Xem tất cả</a>
        </div>

        <!-- Advertisement Banner -->
        <div class="ad-banner">
            <h3>LUÔN LUÔN ÂN TÂM<br>LUÔN LUÔN CHÍNH HÃNG</h3>
            <div class="ad-banner-img">
                <i class="fas fa-box"></i>
            </div>
        </div>

        <!-- Sản phẩm bán chạy Section -->
        <div class="section-title">
            <h2>SẢN PHẨM NỔI BẬT</h2>
        </div>

        <div class="products-grid">
            <?php $__empty_1 = true; $__currentLoopData = $products->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if($product->discount_percentage > 0): ?>
                            <span class="discount-badge">-<?php echo e($product->discount_percentage); ?>%</span>
                        <?php endif; ?>
                        <img src="<?php echo e($product->image ?? 'https://via.placeholder.com/200x200?text=' . urlencode($product->name)); ?><?php echo e($product->image ? '?v=' . md5($product->updated_at) : ''); ?>" alt="<?php echo e($product->name); ?>">
                    </div>
                    <div class="product-info">
                        <div class="product-name">
                            <a href="<?php echo e(route('product.show', $product->id)); ?>"><?php echo e($product->name); ?></a>
                        </div>
                        <?php if($product->rating): ?>
                            <div class="product-rating">
                                <?php for($i = 0; $i < 5; $i++): ?>
                                    <?php if($i < $product->rating): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                        <div class="product-price">
                            <span class="product-price-current"><?php echo e(number_format($product->price, 0, ',', '.')); ?>₫</span>
                            <?php if($product->original_price): ?>
                                <span class="product-price-original"><?php echo e(number_format($product->original_price, 0, ',', '.')); ?>₫</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions" style="display: flex; gap: 8px;">
                            <a href="<?php echo e(route('product.show', $product->id)); ?>" style="flex: 1; display: flex; align-items: center; justify-content: center; background: #00b894; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; font-weight: 600; text-decoration: none; transition: background 0.3s;">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                            <form action="<?php echo e(route('cart.add', $product->id)); ?>" method="POST" style="flex: 1;" class="add-to-cart-form">
                                <?php echo csrf_field(); ?>
                                <button type="submit" style="width: 100%; padding: 8px; background: #ff6b6b; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; font-weight: 600; transition: background 0.3s;">
                                    <i class="fas fa-shopping-cart"></i> Giỏ hàng
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p style="grid-column: 1/-1; text-align: center; padding: 40px;">Chưa có sản phẩm nào</p>
            <?php endif; ?>
        </div>

        <!-- View All Button -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="<?php echo e(route('products.index')); ?>" style="display: inline-block; padding: 12px 40px; background: #333; color: white; border: none; border-radius: 3px; cursor: pointer; font-weight: 600; font-size: 14px; text-decoration: none; transition: background 0.3s;" onmouseover="this.style.background='#555';" onmouseout="this.style.background='#333';">Xem tất cả</a>
        </div>

        <!-- News Section -->
        <div class="section-title">
            <h2>TIN TỨC CÔNG NGHỆ</h2>
            <p>Cập nhật những tin tức mới nhất về phụ kiện công nghệ</p>
        </div>

        <div class="products-grid">
            <div class="product-card">
                <div class="product-image">
                    <img src="/Image/keyboard.jpg" alt="Tin tức">
                </div>
                <div class="product-info">
                    <div class="product-name">
                        <a href="#">TOP 5 bàn phím cơ gaming tốt nhất 2026 - Chọn loại nào phù hợp?</a>
                    </div>
                    <p style="font-size: 11px; color: #999; margin: 8px 0;">📅 03/02/2026 | ✍️ TechStore</p>
                </div>
            </div>

            <div class="product-card">
                <div class="product-image">
                    <img src="/Image/wired.jpg" alt="Tin tức">
                </div>
                <div class="product-info">
                    <div class="product-name">
                        <a href="#">Chuột gaming không dây vs có dây - Cái nào tốt hơn cho gamer?</a>
                    </div>
                    <p style="font-size: 11px; color: #999; margin: 8px 0;">📅 02/02/2026 | ✍️ TechStore</p>
                </div>
            </div>

            <div class="product-card">
                <div class="product-image">
                    <img src="/Image/gaming.jpg" alt="Tin tức">
                </div>
                <div class="product-info">
                    <div class="product-name">
                        <a href="#">Tai nghe gaming chuyên nghiệp - Lựa chọn tốt nhất cho streamer 2026</a>
                    </div>
                    <p style="font-size: 11px; color: #999; margin: 8px 0;">📅 31/01/2026 | ✍️ TechStore</p>
                </div>
            </div>

            <div class="product-card">
                <div class="product-image">
                    <img src="/Image/monitor.jpg" alt="Tin tức">
                </div>
                <div class="product-info">
                    <div class="product-name">
                        <a href="#">Màn hình 4K 144Hz - Chuẩn mực mới cho gaming cao cấp</a>
                    </div>
                    <p style="font-size: 11px; color: #999; margin: 8px 0;">📅 29/01/2026 | ✍️ TechStore</p>
                </div>
            </div>

        </div>

        <div class="section-title">
            <h2>THƯƠNG HIỆU PHỔ BIẾN</h2>
        </div>

        <div class="brands">
            <img src="/images/Logitech.png" alt="Logitech" style="max-height: 60px;">
            <img src="/images/azus.png" alt="ASUS" style="max-height: 60px;">
            <img src="/images/razer.png" alt="Razer" style="max-height: 60px;">
            <img src="/images/corsair.png" alt="Corsair" style="max-height: 60px;">
            <img src="/images/steel.png" alt="SteelSeries" style="max-height: 60px;">
        </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div style="padding: 0px 10px; display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
            <img src="/Image/logo.jpg" alt="TechStore Logo" style="height: 40px; width: auto; border-radius: 4px;">
            <div style="font-weight: 900; letter-spacing: -1px; font-size: 24px;"><span style="color: #1E90FF; font-style: italic;">Tech</span><span style="color: white; text-shadow: -1px 0 #1E90FF, 1px 0 #1E90FF, 0 1px #1E90FF;">Store</span></div>
        </div>
        <div class="footer-content">
            <div class="footer-section">
                <h3>HỖ TRỢ KHÁCH HÀNG</h3>
                <a href="#">Chính sách đổi trả</a>
                <a href="#">Chính sách bảo mật</a>
                <a href="#">Hướng dẫn thanh toán</a>
                <a href="#">Hướng dẫn mua hàng</a>
            </div>
            <div class="footer-section">
                <h3>GIỚI THIỆU</h3>
                <a href="#">Về chúng tôi</a>
                <a href="#">Tuyển dụng</a>
                <a href="#">Tin tức</a>
                <a href="#">Liên hệ</a>
            </div>
            <div class="footer-section">
                <h3>LIÊN HỆ CHÚNG TÔI</h3>
                <p>Địa chỉ: 123 Đường Lý Thường Kiệt, Hà Nội</p>
                <p>Điện thoại: 1900-1234</p>
                <p>Email: support@techstore.com</p>
            </div>
            <div class="footer-section">
                <h3>THEO DÕI CHÚNG TÔI</h3>
                <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
                <a href="#"><i class="fab fa-youtube"></i> YouTube</a>
                <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
                <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 TechStore - Bản quyền thuộc về TechStore. Tất cả các quyền được bảo lưu.</p>
        </div>
    </footer>

    <script>
        // Handle add to cart with flying animation
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get product card and button position
                const button = this.querySelector('button');
                const productCard = this.closest('.product-card');
                const productImage = productCard.querySelector('.product-image');
                const productName = productCard.querySelector('.product-name').textContent;
                
                // Get cart icon position
                const cartIcon = document.querySelector('.nav-icons a[href*="cart"]');
                const cartIconRect = cartIcon.getBoundingClientRect();
                const imageRect = productImage.getBoundingClientRect();
                
                // Create flying element
                const flyingItem = document.createElement('div');
                flyingItem.className = 'flying-item';
                flyingItem.innerHTML = '<i class="fas fa-shopping-cart"></i>';
                flyingItem.style.left = imageRect.left + 'px';
                flyingItem.style.top = imageRect.top + 'px';
                flyingItem.style.width = imageRect.width + 'px';
                flyingItem.style.height = imageRect.height + 'px';
                
                // Calculate offset to cart icon
                const deltaX = cartIconRect.left - imageRect.left;
                const deltaY = cartIconRect.top - imageRect.top;
                
                flyingItem.style.setProperty('--tx', deltaX + 'px');
                flyingItem.style.setProperty('--ty', deltaY + 'px');
                
                document.body.appendChild(flyingItem);

                // Create particle effects
                const particleCount = 8;
                for (let i = 0; i < particleCount; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    
                    const angle = (i / particleCount) * Math.PI * 2;
                    const distance = 80;
                    const px = Math.cos(angle) * distance;
                    const py = Math.sin(angle) * distance;
                    
                    particle.style.left = (imageRect.left + imageRect.width / 2) + 'px';
                    particle.style.top = (imageRect.top + imageRect.height / 2) + 'px';
                    particle.style.width = '8px';
                    particle.style.height = '8px';
                    particle.style.setProperty('--px', px + 'px');
                    particle.style.setProperty('--py', py + 'px');
                    
                    document.body.appendChild(particle);
                    
                    setTimeout(() => particle.remove(), 800);
                }
                
                // Submit form via AJAX
                const formData = new FormData(this);
                const url = this.getAttribute('action');
                
                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        // Update cart count badge
                        const cartCount = data.cart_count || 0;
                        updateCartBadge(cartCount);
                        
                        // Show success message
                        showNotification('Thêm vào giỏ hàng thành công!', 'success');
                    } else {
                        showNotification(data.message || 'Có lỗi xảy ra!', 'error');
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    console.error('Error message:', error.message);
                    showNotification('Có lỗi xảy ra: ' + error.message, 'error');
                })
                .finally(() => {
                    // Remove flying item after animation
                    setTimeout(() => flyingItem.remove(), 800);
                });
            });
        });

        // Update cart badge
        function updateCartBadge(count) {
            const cartLink = document.querySelector('.nav-icons a[href*="cart"]');
            let badge = cartLink.querySelector('span[style*="background"]');
            
            if (!badge && count > 0) {
                // Create badge if doesn't exist
                const newBadge = document.createElement('span');
                newBadge.style.cssText = 'position: absolute; top: -5px; right: -5px; background: #ff6b6b; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;';
                newBadge.textContent = count;
                cartLink.style.position = 'relative';
                cartLink.appendChild(newBadge);
            } else if (badge && count > 0) {
                // Update existing badge
                badge.textContent = count;
            } else if (badge && count === 0) {
                // Remove badge if count is 0
                badge.remove();
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#00b894' : '#ff6b6b'};
                color: white;
                padding: 15px 20px;
                border-radius: 5px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                z-index: 9999;
                animation: slideIn 0.3s ease-out;
                font-size: 14px;
                font-weight: 500;
                max-width: 300px;
                word-wrap: break-word;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Initialize animations in CSS if not already present
        if (!document.querySelector('style[data-notification-animation]')) {
            const style = document.createElement('style');
            style.setAttribute('data-notification-animation', 'true');
            style.textContent = `
                @keyframes slideIn {
                    from {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes slideOut {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        // Device Switcher
        function switchDevice(device, e) {
            e.preventDefault();
            localStorage.setItem('selectedDevice', device);
            document.body.classList.remove('device-mobile', 'device-tablet', 'device-desktop');
            document.body.classList.add('device-' + device);
            
            // Hide header-top after selecting device
            document.querySelector('.header-top').style.display = 'none';
            
            // Save to server
            fetch('/api/device-preference', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ device: device })
            }).catch(err => console.log('Device preference saved locally'));
        }

        // Load saved device preference on page load
        window.addEventListener('load', function() {
            const savedDevice = localStorage.getItem('selectedDevice');
            if (savedDevice) {
                document.body.classList.add('device-' + savedDevice);
                // Hide header-top if device was already selected
                document.querySelector('.header-top').style.display = 'none';
            } else {
                document.body.classList.add('device-desktop');
            }
        });
    </script>

    <!-- Chatbot Widget -->
    <?php echo $__env->make('components.chatbot-widget', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/home.blade.php ENDPATH**/ ?>