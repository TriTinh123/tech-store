<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - TechStore</title>
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

        /* Checkout Container */
        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            margin-bottom: 30px;
        }

        /* Checkout Form */
        .checkout-form {
            background: white;
            border-radius: 5px;
            padding: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #00b894;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #00b894;
            box-shadow: 0 0 5px rgba(0, 184, 148, 0.3);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Payment Methods */
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .payment-method {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method:hover {
            border-color: #00b894;
            background: #f9f9f9;
        }

        .payment-method input[type="radio"] {
            margin-right: 15px;
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        .payment-method.selected {
            border-color: #00b894;
            background: #e8f8f5;
        }

        .payment-info {
            flex: 1;
        }

        .payment-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .payment-desc {
            font-size: 12px;
            color: #999;
        }

        .payment-icon {
            font-size: 24px;
            color: #00b894;
            margin-right: 15px;
        }

        /* Order Summary */
        .order-summary {
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

        .order-items {
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-name {
            flex: 1;
        }

        .item-qty {
            color: #999;
            margin: 0 10px;
        }

        .item-price {
            font-weight: 600;
            color: #ff6b6b;
            min-width: 80px;
            text-align: right;
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
        }

        .checkout-btn:hover {
            background: #00a080;
        }

        .checkout-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .back-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #999;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background: #777;
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

        .footer-bottom {
            border-top: 1px solid #555;
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #aaa;
        }

        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
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
                @auth
                    <span>|</span>
                    <a href="#" style="color: white; text-decoration: none;">{{ Auth::user()->name }}</a>
                    <span>|</span>
                    <a href="{{ route('logout') }}" style="color: white; text-decoration: none; cursor: pointer;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                @else
                    <span>|</span>
                    <a href="{{ route('login') }}" style="color: white; text-decoration: none;">Đăng nhập</a>
                    <span>|</span>
                    <a href="{{ route('register') }}" style="color: white; text-decoration: none;">Đăng kí</a>
                @endauth
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
                <a href="{{ route('cart.index') }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Giỏ hàng</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <div class="page-title">
            <i class="fas fa-credit-card"></i> Thanh toán
        </div>

        @if ($errors->any())
            <div style="background: #ffe8e8; color: #ff6b6b; padding: 15px 20px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ff6b6b;">
                <strong>Lỗi:</strong>
                <ul style="margin-top: 10px; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="checkout-container">
            <!-- Checkout Form -->
            <form action="{{ route('checkout.store') }}" method="POST" class="checkout-form" id="checkoutForm">
                @csrf

                <!-- Customer Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i> Thông tin khách hàng
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="customer_name">Họ tên *</label>
                            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_email">Email *</label>
                            <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="customer_phone">Số điện thoại *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
                    </div>
                </div>

                <!-- Delivery Address -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng
                    </h3>

                    <div class="form-group">
                        <label for="delivery_address">Địa chỉ giao hàng *</label>
                        <textarea id="delivery_address" name="delivery_address" required>{{ old('delivery_address') }}</textarea>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-wallet"></i> Phương thức thanh toán
                    </h3>

                    <div class="payment-methods">
                        <!-- COD -->
                        <label class="payment-method" onclick="selectPayment(this)">
                            <input type="radio" name="payment_method" value="cod" {{ old('payment_method') == 'cod' ? 'checked' : '' }} required>
                            <i class="fas fa-truck payment-icon"></i>
                            <div class="payment-info">
                                <div class="payment-name">Thanh toán khi nhận hàng (COD)</div>
                                <div class="payment-desc">Thanh toán tiền mặt khi nhận hàng</div>
                            </div>
                        </label>

                        <!-- Bank Transfer -->
                        <label class="payment-method" onclick="selectPayment(this)">
                            <input type="radio" name="payment_method" value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }} required>
                            <i class="fas fa-university payment-icon"></i>
                            <div class="payment-info">
                                <div class="payment-name">Chuyển khoản ngân hàng</div>
                                <div class="payment-desc">Chuyển khoản trước khi giao hàng</div>
                            </div>
                        </label>

                        <!-- E-Wallet -->
                        <label class="payment-method" onclick="selectPayment(this)">
                            <input type="radio" name="payment_method" value="e_wallet" {{ old('payment_method') == 'e_wallet' ? 'checked' : '' }} required>
                            <i class="fas fa-mobile-alt payment-icon"></i>
                            <div class="payment-info">
                                <div class="payment-name">Ví điện tử (Momo, ZaloPay, ...)</div>
                                <div class="payment-desc">Thanh toán qua ứng dụng ví điện tử</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-sticky-note"></i> Ghi chú
                    </h3>

                    <div class="form-group">
                        <label for="notes">Ghi chú thêm (tuỳ chọn)</label>
                        <textarea id="notes" name="notes" placeholder="Ví dụ: Giao buổi sáng, cần gói kỹ ...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="checkout-btn">
                    <i class="fas fa-check"></i> Xác nhận đơn hàng
                </button>

                <a href="{{ route('cart.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                </a>
            </form>

            <!-- Order Summary -->
            <div class="order-summary">
                <div class="summary-title">
                    <i class="fas fa-list"></i> Tóm tắt đơn hàng
                </div>

                <div class="order-items">
                    @foreach ($items as $item)
                        <div class="order-item">
                            <div class="item-name">{{ $item['product']->name }}</div>
                            <div class="item-qty">x{{ $item['quantity'] }}</div>
                            <div class="item-price">{{ number_format($item['subtotal'], 0, ',', '.') }}₫</div>
                        </div>
                    @endforeach
                </div>

                <div class="summary-row">
                    <span>Tạm tính:</span>
                    <span>{{ number_format($total, 0, ',', '.') }}₫</span>
                </div>

                <div class="summary-row">
                    <span>Phí vận chuyển:</span>
                    <span><strong style="color: #00b894;">MIỄN PHÍ</strong></span>
                </div>

                <div class="summary-row">
                    <span>Giảm giá:</span>
                    <span>0₫</span>
                </div>

                <div class="summary-row total">
                    <span>Tổng cộng:</span>
                    <span>{{ number_format($total, 0, ',', '.') }}₫</span>
                </div>

                <div style="background: #f5f5f5; padding: 12px; border-radius: 5px; margin-top: 15px; font-size: 12px;">
                    <p><i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong></p>
                    <ul style="margin-left: 20px; margin-top: 8px; color: #666;">
                        <li>Kiểm tra kỹ thông tin giao hàng</li>
                        <li>Đơn hàng sẽ được xác nhận sau 5 phút</li>
                        <li>Bạn sẽ nhận email xác nhận</li>
                    </ul>
                </div>
            </div>
        </div>
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
        function selectPayment(element) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(method => {
                method.classList.remove('selected');
            });
            // Add selected class to clicked element
            element.classList.add('selected');
            // Check the radio button
            element.querySelector('input[type="radio"]').checked = true;
        }

        // Initialize selected payment method on page load
        document.addEventListener('DOMContentLoaded', function() {
            const selectedPayment = document.querySelector('input[name="payment_method"]:checked').closest('.payment-method');
            if (selectedPayment) {
                selectedPayment.classList.add('selected');
            }
        });

        // Form validation
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const phone = document.getElementById('customer_phone').value;
            const phoneRegex = /^[0-9]{10,11}$/;
            
            if (!phoneRegex.test(phone)) {
                e.preventDefault();
                alert('Số điện thoại không hợp lệ. Vui lòng nhập 10-11 chữ số.');
                return false;
            }
        });
    </script>
    <!-- Chatbot Widget -->
    @include('components.chatbot-widget')</body>
</html>
