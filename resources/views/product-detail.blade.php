<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - TechStore</title>
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

        /* Breadcrumb */
        .breadcrumb {
            background: white;
            padding: 15px 20px;
            max-width: 1200px;
            margin: 20px auto;
            font-size: 12px;
        }

        .breadcrumb a {
            color: #00b894;
            text-decoration: none;
            margin: 0 5px;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Product Detail Section */
        .product-detail {
            background: white;
            border-radius: 5px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .product-image {
            position: relative;
            width: 100%;
            height: 400px;
            background: #f5f5f5;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .product-discount {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff6b6b;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
        }

        .product-info h1 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #333;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .product-rating i {
            color: #ffa500;
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .price-current {
            font-size: 32px;
            font-weight: bold;
            color: #ff6b6b;
        }

        .price-original {
            font-size: 18px;
            text-decoration: line-through;
            color: #999;
        }

        .product-stock {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .product-stock.in-stock {
            background: #e8f8f5;
            color: #00b894;
        }

        .product-stock.out-stock {
            background: #ffe8e8;
            color: #ff6b6b;
        }

        .product-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }

        .quantity-selector button {
            background: #f5f5f5;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            font-weight: 600;
        }

        .quantity-selector input {
            border: none;
            width: 50px;
            text-align: center;
            padding: 8px;
        }

        .add-to-cart-btn {
            flex: 1;
            padding: 15px;
            background: #00b894;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .add-to-cart-btn:hover {
            background: #00a080;
        }

        .buy-now-btn {
            flex: 1;
            padding: 15px;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .buy-now-btn:hover {
            background: #ff5555;
        }

        .product-features {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .product-features h3 {
            margin-bottom: 15px;
            font-size: 16px;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }

        .feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #00b894;
            font-weight: bold;
        }

        /* Description Section */
        .section-title {
            background: white;
            padding: 20px;
            margin: 30px 0 20px 0;
            border-left: 4px solid #00b894;
            border-radius: 3px;
            font-size: 20px;
            font-weight: 600;
        }

        .description-content {
            background: white;
            padding: 30px;
            border-radius: 5px;
            line-height: 1.8;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* Reviews Section */
        .reviews-section {
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .review-item {
            border-bottom: 1px solid #eee;
            padding: 20px 0;
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .review-author {
            font-weight: 600;
        }

        .review-date {
            color: #999;
        }

        .review-rating {
            color: #ffa500;
            margin-bottom: 10px;
        }

        .review-text {
            line-height: 1.6;
        }

        .review-form {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }

        .review-form h3 {
            margin-bottom: 15px;
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
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #00b894;
        }

        .submit-btn {
            background: #00b894;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        .submit-btn:hover {
            background: #00a080;
        }

        /* Related Products */
        .related-products {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .product-card {
            background: white;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .product-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transform: translateY(-5px);
        }

        .product-image-small {
            position: relative;
            width: 100%;
            height: 150px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image-small img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .product-info-small {
            padding: 15px;
        }

        .product-name {
            font-size: 12px;
            margin-bottom: 8px;
            line-height: 1.4;
            min-height: 28px;
        }

        .product-name a {
            text-decoration: none;
            color: #333;
        }

        .product-name a:hover {
            color: #00b894;
        }

        .product-price-small {
            font-size: 14px;
            font-weight: bold;
            color: #ff6b6b;
            margin-bottom: 8px;
        }

        .product-btn {
            width: 100%;
            padding: 8px;
            background: #00b894;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .product-btn:hover {
            background: #00a080;
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

        /* Message */
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

        .alert-error {
            background: #ffe8e8;
            color: #ff6b6b;
            border: 1px solid #ff6b6b;
        }

        @media (max-width: 768px) {
            .detail-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .product-image {
                height: 300px;
            }

            .price-current {
                font-size: 24px;
            }

            .product-actions {
                flex-direction: column;
            }

            .related-products {
                grid-template-columns: repeat(2, 1fr);
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

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a> /
        <a href="/?category={{ $product->category }}">{{ ucfirst($product->category) }}</a> /
        <span>{{ $product->name }}</span>
    </div>

    <!-- Main Container -->
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <!-- Product Detail -->
        <div class="product-detail">
            <div class="detail-grid">
                <!-- Product Image -->
                <div>
                    <div class="product-image">
                        @if($product->discount_percentage > 0)
                            <span class="product-discount">-{{ $product->discount_percentage }}%</span>
                        @endif
                        <img src="{{ $product->image ?? 'https://via.placeholder.com/400x400?text=' . urlencode($product->name) }}" alt="{{ $product->name }}">
                    </div>
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <h1>{{ $product->name }}</h1>

                    <div class="product-rating">
                        @for($i = 0; $i < 5; $i++)
                            @if($i < ($product->rating ?? 0))
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                        <span>({{ $product->reviews_count ?? 0 }} đánh giá)</span>
                    </div>

                    <div class="product-price">
                        <span class="price-current">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                        @if($product->original_price)
                            <span class="price-original">{{ number_format($product->original_price, 0, ',', '.') }}₫</span>
                        @endif
                    </div>

                    <div class="product-stock @if(($product->stock ?? 0) > 0) in-stock @else out-stock @endif">
                        @if(($product->stock ?? 0) > 0)
                            <i class="fas fa-check-circle"></i> Còn hàng ({{ $product->stock ?? 0 }} sản phẩm)
                        @else
                            <i class="fas fa-times-circle"></i> Hết hàng
                        @endif
                    </div>

                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="product-actions">
                        @csrf
                        <div class="quantity-selector">
                            <button type="button" onclick="decreaseQuantity()">-</button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" readonly>
                            <button type="button" onclick="increaseQuantity()">+</button>
                        </div>
                        <button type="submit" class="add-to-cart-btn" @if(($product->stock ?? 0) <= 0) disabled @endif>
                            <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                        </button>
                    </form>

                    <div class="product-features">
                        <h3><i class="fas fa-star"></i> Điểm nổi bật</h3>
                        <ul class="feature-list">
                            <li>Hàng chính hãng 100%</li>
                            <li>Bảo hành 2 năm trọn gói</li>
                            <li>Giao hàng miễn phí toàn quốc</li>
                            <li>Hỗ trợ khách hàng 24/7</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="section-title">Mô tả sản phẩm</div>
        <div class="description-content">
            {!! nl2br($product->description ?? 'Không có mô tả') !!}
        </div>

        <!-- Reviews Section -->
        <div class="section-title">Đánh giá sản phẩm</div>
        <div class="reviews-section">
            @if(count($product->reviews ?? []) > 0)
                @foreach($product->reviews ?? [] as $review)
                    <div class="review-item">
                        <div class="review-header">
                            <span class="review-author">{{ $review->user_name ?? 'Ẩn danh' }}</span>
                            <span class="review-date">{{ $review->created_at->format('d/m/Y H:i') ?? '' }}</span>
                        </div>
                        <div class="review-rating">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < ($review->rating ?? 0))
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="review-text">
                            {{ $review->comment ?? '' }}
                        </div>
                    </div>
                @endforeach
            @else
                <p style="text-align: center; color: #999; padding: 30px;">Chưa có đánh giá nào</p>
            @endif

            <div class="review-form">
                <h3>Viết đánh giá của bạn</h3>
                @if($errors->any())
                    <div style="background: #fee; color: #c00; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('product.review', $product->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="user_name">Tên của bạn</label>
                        <input type="text" id="user_name" name="user_name" placeholder="Nhập tên..." value="{{ old('user_name', auth()->user()->name ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="user_email">Email</label>
                        <input type="email" id="user_email" name="user_email" placeholder="Nhập email..." value="{{ old('user_email', auth()->user()->email ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="rating">Đánh giá</label>
                        <select id="rating" name="rating" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" required>
                            <option value="">-- Chọn đánh giá --</option>
                            <option value="5" {{ old('rating') == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ Rất tốt</option>
                            <option value="4" {{ old('rating') == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ Tốt</option>
                            <option value="3" {{ old('rating') == 3 ? 'selected' : '' }}>⭐⭐⭐ Bình thường</option>
                            <option value="2" {{ old('rating') == 2 ? 'selected' : '' }}>⭐⭐ Không tốt</option>
                            <option value="1" {{ old('rating') == 1 ? 'selected' : '' }}>⭐ Rất tệ</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="comment">Bình luận</label>
                        <textarea id="comment" name="comment" placeholder="Chia sẻ cảm nhận của bạn..." required style="min-height: 100px;">{{ old('comment') }}</textarea>
                    </div>
                    <button type="submit" class="submit-btn">Gửi đánh giá</button>
                </form>
            </div>
        </div>

        <!-- Related Products -->
        @if(count($relatedProducts) > 0)
            <div class="section-title">Sản phẩm liên quan</div>
            <div class="related-products">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="product-card">
                        <div class="product-image-small">
                            <img src="{{ $relatedProduct->image ?? 'https://via.placeholder.com/200x200?text=' . urlencode($relatedProduct->name) }}" alt="{{ $relatedProduct->name }}">
                        </div>
                        <div class="product-info-small">
                            <div class="product-name">
                                <a href="{{ route('product.show', $relatedProduct->id) }}">{{ $relatedProduct->name }}</a>
                            </div>
                            <div class="product-price-small">
                                {{ number_format($relatedProduct->price, 0, ',', '.') }}₫
                            </div>
                            <form action="{{ route('cart.add', $relatedProduct->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="product-btn">Thêm vào giỏ</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
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
        function increaseQuantity() {
            const input = document.getElementById('quantity');
            input.value = parseInt(input.value) + 1;
        }

        function decreaseQuantity() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }
    </script>

    <!-- Chatbot Widget -->
    @include('components.chatbot-widget')
</body>
</html>
