<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - TechStore</title>
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

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-title {
            background: white;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #00b894;
            border-radius: 3px;
            font-size: 24px;
            font-weight: 600;
        }

        .cart-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            margin-bottom: 30px;
        }

        .cart-items {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .cart-header {
            background: #f5f5f5;
            padding: 15px 20px;
            display: grid;
            grid-template-columns: 80px 1fr 100px 100px 80px;
            gap: 15px;
            font-weight: 600;
            font-size: 14px;
        }

        .cart-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: grid;
            grid-template-columns: 80px 1fr 100px 100px 80px;
            gap: 15px;
            align-items: center;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 80px;
            height: 80px;
            background: #f5f5f5;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .item-image img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .item-info h4 {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .item-info p {
            font-size: 12px;
            color: #999;
        }

        .item-price {
            font-weight: 600;
            color: #ff6b6b;
        }

        .quantity-input {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 3px;
            overflow: hidden;
        }

        .quantity-input button {
            background: #f5f5f5;
            border: none;
            width: 25px;
            height: 30px;
            cursor: pointer;
        }

        .quantity-input input {
            border: none;
            width: 50px;
            text-align: center;
            padding: 5px;
        }

        .remove-btn {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s;
        }

        .remove-btn:hover {
            background: #ff5555;
        }

        /* Cart Summary */
        .cart-summary {
            background: white;
            border-radius: 5px;
            padding: 20px;
            height: fit-content;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .summary-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .summary-row.total {
            font-size: 16px;
            font-weight: 600;
            color: #ff6b6b;
            border-top: 1px solid #eee;
            padding-top: 12px;
            margin-top: 12px;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: #00b894;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 15px;
            transition: background 0.3s;
            text-align: center;
            text-decoration: none;
        }

        .checkout-btn:hover {
            background: #00a080;
            color: white;
        }

        .clear-cart-btn {
            width: 100%;
            padding: 10px;
            background: #999;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s;
        }

        .clear-cart-btn:hover {
            background: #777;
        }

        /* Empty Cart */
        .empty-cart {
            background: white;
            border-radius: 5px;
            padding: 60px 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .empty-cart i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 15px;
        }

        .empty-cart p {
            font-size: 16px;
            color: #999;
            margin-bottom: 20px;
        }

        .continue-shopping {
            display: inline-block;
            padding: 12px 30px;
            background: #00b894;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .continue-shopping:hover {
            background: #00a080;
        }

        /* Alert */
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background: #e8f8f5;
            color: #00b894;
            border: 1px solid #00b894;
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
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            margin-bottom: 30px;
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

        @media (max-width: 768px) {
            .cart-container {
                grid-template-columns: 1fr;
            }

            .cart-header,
            .cart-item {
                grid-template-columns: 60px 1fr 60px;
                gap: 10px;
            }

            .item-price,
            .quantity-input {
                display: none;
            }

            .footer-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Header Top -->
    <div class="header-top">
        <div class="header-top-content">
            <div class="header-top-left">
                <span>Hôm nay là một ngày tốt lành!</span>
            </div>
            <div class="header-top-right">
                <a href="#" style="color: white; text-decoration: none;">Trợ giúp</a>
                <?php if(auth()->guard()->check()): ?>
                    <span>|</span>
                    <a href="#" style="color: white; text-decoration: none;"><?php echo e(Auth::user()->name); ?></a>
                    <span>|</span>
                    <a href="<?php echo e(route('logout')); ?>" style="color: white; text-decoration: none; cursor: pointer;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a>
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;"><?php echo csrf_field(); ?></form>
                <?php else: ?>
                    <span>|</span>
                    <a href="<?php echo e(route('login')); ?>" style="color: white; text-decoration: none;">Đăng nhập</a>
                    <span>|</span>
                    <a href="<?php echo e(route('register')); ?>" style="color: white; text-decoration: none;">Đăng kí</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-content">
            <a href="/" style="text-decoration: none;">
                <div class="logo">Tech<span>Store</span></div>
            </a>
            <div class="search-container">
                <input type="text" placeholder="Tìm kiếm sản phẩm...">
                <button><i class="fas fa-search"></i></button>
            </div>
            <div class="nav-icons">
                <a href="#">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Địa chỉ</span>
                </a>
                <a href="#">
                    <i class="fas fa-phone"></i>
                    <span>Liên hệ</span>
                </a>
                <a href="<?php echo e(route('cart.index')); ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Giỏ hàng</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <div class="page-title">
            <i class="fas fa-shopping-cart"></i> Giỏ hàng (<?php echo e(count($items)); ?> sản phẩm)
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(count($items) > 0): ?>
            <div class="cart-container">
                <!-- Cart Items -->
                <div class="cart-items">
                    <div class="cart-header">
                        <div>Ảnh</div>
                        <div>Sản phẩm</div>
                        <div>Giá</div>
                        <div>Số lượng</div>
                        <div>Hành động</div>
                    </div>

                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <img src="<?php echo e($item['product']->image ?? 'https://via.placeholder.com/80x80?text=Sản+phẩm'); ?>" alt="<?php echo e($item['product']->name); ?>">
                            </div>
                            <div class="item-info">
                                <h4><?php echo e($item['product']->name); ?></h4>
                                <p>Mã: <?php echo e($item['product']->id); ?></p>
                            </div>
                            <div class="item-price">
                                <?php echo e(number_format($item['product']->price, 0, ',', '.')); ?>₫
                            </div>
                            <form action="<?php echo e(route('cart.update', $item['product']->id)); ?>" method="POST" style="display: inline;">
                                <?php echo csrf_field(); ?>
                                <div class="quantity-input">
                                    <button type="button" onclick="decreaseQuantity(this)">−</button>
                                    <input type="number" name="quantity" value="<?php echo e($item['quantity']); ?>" min="1" onchange="this.form.submit()">
                                    <button type="button" onclick="increaseQuantity(this)">+</button>
                                </div>
                            </form>
                            <form action="<?php echo e(route('cart.remove', $item['product']->id)); ?>" method="POST" style="display: inline;" class="remove-form" data-product-id="<?php echo e($item['product']->id); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="remove-btn">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Cart Summary -->
                <div class="cart-summary">
                    <div class="summary-title">Tóm tắt đơn hàng</div>

                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <span><?php echo e(number_format($total, 0, ',', '.')); ?>₫</span>
                    </div>

                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <span>Miễn phí</span>
                    </div>

                    <div class="summary-row">
                        <span>Giảm giá:</span>
                        <span>0₫</span>
                    </div>

                    <div class="summary-row total">
                        <span>Tổng cộng:</span>
                        <span><?php echo e(number_format($total, 0, ',', '.')); ?>₫</span>
                    </div>

                    <a href="<?php echo e(route('checkout.show')); ?>" class="checkout-btn">
                        <i class="fas fa-credit-card"></i> Thanh toán
                    </a>

                    <a href="/" class="continue-shopping" style="display: block; text-align: center;">
                        <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                    </a>

                    <form action="<?php echo e(route('cart.clear')); ?>" method="POST" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="clear-cart-btn" onclick="return confirm('Bạn chắc chắn muốn xóa toàn bộ giỏ hàng?')">
                            <i class="fas fa-trash"></i> Xóa giỏ hàng
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Giỏ hàng của bạn trống</p>
                <a href="/" class="continue-shopping">
                    <i class="fas fa-arrow-left"></i> Quay lại mua sắm
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
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
        function increaseQuantity(button) {
            const input = button.parentElement.querySelector('input');
            input.value = parseInt(input.value) + 1;
        }

        function decreaseQuantity(button) {
            const input = button.parentElement.querySelector('input');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        // Handle remove button clicks with AJAX
        document.querySelectorAll('.remove-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const productId = this.dataset.productId;
                const csrfToken = this.querySelector('[name="_token"]').value;
                const cartItem = this.closest('.cart-item');
                
                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove item from cart display with animation
                        cartItem.style.transition = 'all 0.3s ease-out';
                        cartItem.style.opacity = '0';
                        cartItem.style.transform = 'translateX(-20px)';
                        
                        setTimeout(() => {
                            cartItem.remove();
                            updateCartDisplay(data.cart_count, data.total);
                        }, 300);
                    } else {
                        alert(data.message || 'Lỗi xóa sản phẩm');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi khi xóa sản phẩm');
                });
            });
        });

        // Update cart display
        function updateCartDisplay(cartCount, total) {
            // Update cart badge if it exists
            const cartBadge = document.querySelector('.nav-icons a i')?.closest('a')?.querySelector('span');
            if (cartBadge && cartCount > 0) {
                cartBadge.textContent = cartCount;
            }
            
            // Update total display
            const totalElements = document.querySelectorAll('.summary-row span:last-child');
            if (totalElements.length > 0) {
                totalElements[0].textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total).replace('₫', '') + '₫';
            }
            
            // If cart is empty, show message
            if (cartCount === 0) {
                const cartItems = document.querySelector('.cart-items');
                if (cartItems && cartItems.querySelectorAll('.cart-item').length === 0) {
                    cartItems.innerHTML = '<p style="text-align: center; padding: 40px;">Giỏ hàng của bạn trống rỗng</p>';
                }
            }
        }
    </script>

    <!-- Chatbot Widget -->
    <?php echo $__env->make('components.chatbot-widget', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/cart.blade.php ENDPATH**/ ?>