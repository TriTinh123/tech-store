<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'TechStore'); ?> - E-Commerce Platform</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        :root {
            --primary: #3b82f6;
            --secondary: #2563eb;
            --success: #48bb78;
            --danger: #f5576c;
            --warning: #f6ad55;
            --info: #4299e1;
            --dark: #1a202c;
            --light: #f7fafc;
            --gray: #cbd5e0;
        }

        body {
            background-color: #f8f9fa;
            color: #2d3748;
        }

        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 0 !important;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }

        .navbar-brand * {
            color: inherit !important;
            text-decoration: none !important;
        }

        .navbar-brand img {
            display: inline-block !important;
            height: 40px !important;
            width: auto !important;
            border-radius: 4px !important;
        }

        .navbar-brand span {
            font-weight: 900 !important;
            letter-spacing: -1px !important;
            font-size: 28px !important;
        }

        .navbar-brand span span:nth-child(1) {
            color: #1E90FF !important;
            font-style: italic !important;
        }

        .navbar-brand span span:nth-child(2) {
            color: white !important;
            text-shadow: -1px 0 #1E90FF, 1px 0 #1E90FF, 0 1px #1E90FF !important;
        }

        .nav-link {
            color: #2d3748 !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }

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

        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }

        .footer h5 {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
            margin-top: 2rem;
            text-align: center;
            color: rgba(255,255,255,0.7);
        }

        /* Main Content */
        main {
            min-height: 60vh;
            padding: 2rem 0;
        }

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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top" style="background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 0;">
        <div class="container-fluid" style="padding: 15px 20px; max-width: 1200px; margin: 0 auto;">
            <a class="navbar-brand" href="<?php echo e(route('home')); ?>" style="display: flex; align-items: center; gap: 10px; padding: 0; border: none; background: none;">
                <img src="/Image/logo.jpg" alt="TechStore Logo" style="height: 40px; width: auto; border-radius: 4px;">
                <span style="font-weight: 900; letter-spacing: -1px; font-size: 28px;"><span style="color: #1E90FF !important; font-style: italic;">Tech</span><span style="color: white !important; text-shadow: -1px 0 #1E90FF, 1px 0 #1E90FF, 0 1px #1E90FF;">Store</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('home')); ?>">
                            <i class="fas fa-home"></i> Trang Chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('home')); ?>#products">
                            <i class="fas fa-box"></i> Sản Phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="event.preventDefault(); toggleChatbot();" style="color: var(--primary); font-weight: 600;">
                            <i class="fas fa-headset"></i> Tư Vấn Viên
                            <span class="chat-badge-navbar" id="chat-badge-navbar" style="display: none; margin-left: 4px;">●</span>
                        </a>
                    </li>

                    <?php if(auth()->guard()->check()): ?>
                        <?php if(Auth::user()->isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('admin')); ?>" style="color: var(--danger) !important;">
                                    <i class="fas fa-crown"></i> Admin
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?php echo e(Auth::user()->name); ?>

                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="<?php echo e(route('profile.show')); ?>">
                                    <i class="fas fa-user"></i> Hồ Sơ
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('orders.index')); ?>">
                                    <i class="fas fa-shopping-bag"></i> Đơn Hàng
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><form method="POST" action="<?php echo e(route('logout')); ?>" style="margin: 0;">
                                    <?php echo csrf_field(); ?>
                                    <button class="dropdown-item" type="submit">
                                        <i class="fas fa-sign-out-alt"></i> Đăng Xuất
                                    </button>
                                </form></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('login')); ?>">
                                <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('register')); ?>">
                                <i class="fas fa-user-plus"></i> Đăng Ký
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?php echo e(route('cart.index')); ?>" style="margin-left: 1rem;">
                            <i class="fas fa-shopping-cart" style="font-size: 1.2rem;"></i>
                            <span class="cart-badge" id="cart-count">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('info')): ?>
        <div class="alert alert-info alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-info-circle"></i> <?php echo e(session('info')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <strong>Lỗi:</strong>
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div style="padding: 0px 0; display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <img src="/Image/logo.jpg" alt="TechStore Logo" style="height: 40px; width: auto; border-radius: 4px;">
                <div style="font-weight: 900; letter-spacing: -1px; font-size: 24px;"><span style="color: #1E90FF; font-style: italic;">Tech</span><span style="color: white; text-shadow: -1px 0 #1E90FF, 1px 0 #1E90FF, 0 1px #1E90FF;">Store</span></div>
            </div>
            
            <div class="row mt-0">
                <div class="col-md-3 mb-4">
                    <h5>HỖ TRỢ KHÁCH HÀNG</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Chính sách đổi trả</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Hướng dẫn thanh toán</a></li>
                        <li><a href="#">Hướng dẫn mua hàng</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>GIỚI THIỆU</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Về chúng tôi</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                        <li><a href="#">Tin tức</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>LIÊN HỆ CHÚNG TÔI</h5>
                    <p>Địa chỉ: 123 Đường Lý Thường Kiệt, Hà Nội</p>
                    <p>Điện thoại: 1900-1234</p>
                    <p>Email: support@techstore.com</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>THEO DÕI CHÚNG TÔI</h5>
                    <ul class="list-unstyled">
                        <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                        <li><a href="#"><i class="fab fa-youtube"></i> YouTube</a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2026 TechStore - Bản quyền thuộc về TechStore. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Cart Count Script -->
    <script>
        function updateCartCount() {
            fetch('<?php echo e(route("cart.index")); ?>')
                .then(response => response.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const cartCount = doc.querySelector('#cart-count');
                    if (cartCount) {
                        document.getElementById('cart-count').textContent = cartCount.textContent;
                    }
                })
                .catch(error => console.error('Error updating cart count:', error));
        }

        // Update cart count on page load
        document.addEventListener('DOMContentLoaded', updateCartCount);

        // Update when add to cart
        document.addEventListener('cartUpdated', updateCartCount);
    </script>

    <?php echo $__env->yieldContent('scripts'); ?>

    <!-- Chatbot Widget -->
    <?php echo $__env->make('components.chatbot-widget', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/layouts/app.blade.php ENDPATH**/ ?>