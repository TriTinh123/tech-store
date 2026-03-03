{{-- @var \App\Models\User $user --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - {{ config('app.name', 'Laravel') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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
            --dark: #1a202c;
            --light: #f7fafc;
            --gray: #cbd5e0;
        }

        body {
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            color: #2d3748;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sidebar-logo {
            font-size: 1.8rem;
        }

        .sidebar-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
        }

        .sidebar-nav {
            padding: 1.5rem 0;
        }

        .nav-item {
            padding: 1rem 1.5rem;
            margin: 0.5rem 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-left: 3px solid transparent;
            font-weight: 500;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: white;
        }

        .nav-item.active {
            background: rgba(255,255,255,0.2);
            color: white;
            border-left-color: white;
        }

        .nav-icon {
            font-size: 1.2rem;
        }

        .logout-btn {
            width: calc(100% - 3rem);
            padding: 1rem 1.5rem;
            margin: 0.5rem 0.5rem;
            background: rgba(245, 87, 108, 0.2);
            border: 1px solid rgba(245, 87, 108, 0.5);
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.75rem;
            font-size: 1rem;
        }

        .logout-btn:hover {
            background: var(--danger);
            border-color: var(--danger);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
        }

        /* Top Navbar */
        .top-navbar {
            background: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark);
        }

        .user-role {
            font-size: 0.8rem;
            color: var(--gray);
        }

        /* Content Area */
        .content {
            padding: 2rem;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid var(--primary);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .stat-card.success {
            border-left-color: var(--success);
        }

        .stat-card.warning {
            border-left-color: var(--warning);
        }

        .stat-card.danger {
            border-left-color: var(--danger);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
        }

        .stat-change {
            font-size: 0.8rem;
            margin-top: 0.5rem;
            color: var(--success);
        }

        /* Table */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--light);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid var(--gray);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray);
        }

        tr:hover {
            background: var(--light);
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #38a169;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #e63946;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray);
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(72, 187, 120, 0.1);
            color: var(--success);
        }

        .badge-warning {
            background: rgba(246, 173, 85, 0.1);
            color: var(--warning);
        }

        .badge-danger {
            background: rgba(245, 87, 108, 0.1);
            color: var(--danger);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transition: width 0.3s;
            }

            .sidebar.active {
                width: 280px;
            }

            .main-content {
                margin-left: 0;
            }

            .toggle-sidebar {
                display: flex;
            }

            .content {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .top-navbar {
                padding: 1rem;
            }

            .navbar-right {
                gap: 1rem;
            }

            .user-details {
                display: none;
            }
        }

        .toggle-sidebar {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.5rem;
            color: var(--dark);
        }

        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <span class="sidebar-logo">⚙️</span>
                <h1 class="sidebar-title">Admin</h1>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('admin') }}" class="nav-item active" onclick="switchTab(event, 'dashboard')">
                    <span class="nav-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.products') }}" class="nav-item">
                    <span class="nav-icon">📦</span>
                    <span>Sản phẩm</span>
                </a>
                <a href="{{ route('admin.orders') }}" class="nav-item">
                    <span class="nav-icon">📋</span>
                    <span>Đơn hàng</span>
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item">
                    <span class="nav-icon">👥</span>
                    <span>Khách hàng</span>
                </a>
                <a href="{{ route('admin.categories') }}" class="nav-item">
                    <span class="nav-icon">📂</span>
                    <span>Danh mục</span>
                </a>
                <a href="{{ route('admin.logs.login') }}" class="nav-item">
                    <span class="nav-icon">🔐</span>
                    <span>Lịch sử đăng nhập</span>
                </a>
                <a href="{{ route('admin.logs.system') }}" class="nav-item">
                    <span class="nav-icon">📜</span>
                    <span>Nhật ký hệ thống</span>
                </a>
                <a href="{{ route('home') }}" class="nav-item">
                    <span class="nav-icon">🏠</span>
                    <span>Trang chủ</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="nav-icon">🚪</span>
                        <span>Đăng xuất</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <div>
                    <button class="toggle-sidebar" id="toggle-btn" onclick="toggleSidebar()">☰</button>
                    <h1 class="navbar-title" id="navbar-title">Dashboard</h1>
                </div>
                
                <div class="navbar-right">
                    <div class="user-info">
                        @php
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                        @endphp
                        <div class="user-avatar">{{ substr($user->name, 0, 1) }}</div>
                        <div class="user-details">
                            <span class="user-name">{{ $user->name }}</span>
                            <span class="user-role">{{ $user->role === 'admin' ? '👑 Administrator' : '👤 Người dùng' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content">
                
                <!-- Dashboard Section -->
                <div id="dashboard" class="content-section active">
                    <!-- Stats -->
                    <div class="stats-grid">
                        <div class="stat-card success">
                            <div class="stat-icon">📊</div>
                            <div class="stat-label">Tổng doanh thu</div>
                            <div class="stat-value">₫{{ number_format($totalRevenue, 0, ',', '.') }}</div>
                            <div class="stat-change">+12% so với tháng trước</div>
                        </div>

                        <div class="stat-card warning">
                            <div class="stat-icon">📦</div>
                            <div class="stat-label">Tổng đơn hàng</div>
                            <div class="stat-value">{{ $totalOrders }}</div>
                            <div class="stat-change">+5 so với tháng trước</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">🛍️</div>
                            <div class="stat-label">Sản phẩm</div>
                            <div class="stat-value">{{ $totalProducts }}</div>
                            <div class="stat-change">Tổng sản phẩm trong kho</div>
                        </div>

                        <div class="stat-card danger">
                            <div class="stat-icon">👥</div>
                            <div class="stat-label">Khách hàng</div>
                            <div class="stat-value">{{ $totalUsers }}</div>
                            <div class="stat-change">Người dùng đã đăng ký</div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="card">
                        <h2 class="card-title">📋 Đơn hàng gần đây</h2>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#ORD001</td>
                                        <td>Nguyễn Văn A</td>
                                        <td>Intel Core i9-14900K</td>
                                        <td>$589.99</td>
                                        <td><span class="badge badge-success">✓ Thành công</span></td>
                                        <td>24/01/2026</td>
                                    </tr>
                                    <tr>
                                        <td>#ORD002</td>
                                        <td>Trần Thị B</td>
                                        <td>NVIDIA RTX 4090</td>
                                        <td>$1,699.99</td>
                                        <td><span class="badge badge-warning">⏳ Đang giao</span></td>
                                        <td>24/01/2026</td>
                                    </tr>
                                    <tr>
                                        <td>#ORD003</td>
                                        <td>Lê Minh C</td>
                                        <td>Samsung 990 Pro 2TB</td>
                                        <td>$189.99</td>
                                        <td><span class="badge badge-success">✓ Thành công</span></td>
                                        <td>23/01/2026</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div id="products" class="content-section">
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                            <h2 class="card-title" style="margin-bottom: 0;">📦 Quản lý sản phẩm</h2>
                            <button class="btn btn-primary">➕ Thêm sản phẩm</button>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tên sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th>Giá</th>
                                        <th>Kho</th>
                                        <th>Đánh giá</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Intel Core i9-14900K</td>
                                        <td>CPU</td>
                                        <td>$589.99</td>
                                        <td>25</td>
                                        <td>⭐⭐⭐⭐⭐</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">✏️ Sửa</button>
                                            <button class="btn btn-sm btn-danger">🗑️ Xóa</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>NVIDIA RTX 4090</td>
                                        <td>GPU</td>
                                        <td>$1,699.99</td>
                                        <td>12</td>
                                        <td>⭐⭐⭐⭐⭐</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">✏️ Sửa</button>
                                            <button class="btn btn-sm btn-danger">🗑️ Xóa</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Orders Section -->
                <div id="orders" class="content-section">
                    <div class="card">
                        <h2 class="card-title">📋 Quản lý đơn hàng</h2>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#ORD001</td>
                                        <td>Nguyễn Văn A</td>
                                        <td>$589.99</td>
                                        <td><span class="badge badge-success">✓ Hoàn thành</span></td>
                                        <td>24/01/2026</td>
                                        <td><button class="btn btn-sm btn-primary">👁️ Xem</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Customers Section -->
                <div id="customers" class="content-section">
                    <div class="card">
                        <h2 class="card-title">👥 Quản lý khách hàng</h2>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tên khách hàng</th>
                                        <th>Email</th>
                                        <th>Tổng mua</th>
                                        <th>Số đơn</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Nguyễn Văn A</td>
                                        <td>nguyenvana@test.com</td>
                                        <td>$2,450.50</td>
                                        <td>5</td>
                                        <td><span class="badge badge-success">✓ Hoạt động</span></td>
                                        <td><button class="btn btn-sm btn-primary">👁️ Xem</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reports Section -->
                <div id="reports" class="content-section">
                    <div class="card">
                        <h2 class="card-title">📈 Báo cáo thống kê</h2>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon">💰</div>
                                <div class="stat-label">Doanh thu tháng</div>
                                <div class="stat-value">$45,231</div>
                            </div>
                            <div class="stat-card success">
                                <div class="stat-icon">📊</div>
                                <div class="stat-label">Tổng đơn hàng</div>
                                <div class="stat-value">856</div>
                            </div>
                            <div class="stat-card warning">
                                <div class="stat-icon">📦</div>
                                <div class="stat-label">Sản phẩm bán</div>
                                <div class="stat-value">2,145</div>
                            </div>
                            <div class="stat-card danger">
                                <div class="stat-icon">👥</div>
                                <div class="stat-label">Khách hàng mới</div>
                                <div class="stat-value">342</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Section -->
                <div id="settings" class="content-section">
                    <div class="card">
                        <h2 class="card-title">⚙️ Cài đặt hệ thống</h2>
                        <form>
                            <div class="form-group">
                                <label class="form-label">Tên cửa hàng</label>
                                <input type="text" class="form-input" value="TechStore" placeholder="Nhập tên cửa hàng">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email liên hệ</label>
                                <input type="email" class="form-input" value="contact@techstore.com" placeholder="Nhập email">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-input" value="+84 123 456 789" placeholder="Nhập số điện thoại">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Địa chỉ</label>
                                <textarea class="form-textarea" placeholder="Nhập địa chỉ">123 Nguyễn Huệ, TP.HCM</textarea>
                            </div>

                            <button type="submit" class="btn btn-success">💾 Lưu cài đặt</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function switchTab(e, tabName) {
            e.preventDefault();
            
            // Hide all sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(s => s.classList.remove('active'));
            
            // Remove active from all nav items
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => item.classList.remove('active'));
            
            // Show selected section
            document.getElementById(tabName).classList.add('active');
            
            // Add active to clicked nav item
            e.target.closest('.nav-item').classList.add('active');
            
            // Update navbar title
            const titleMap = {
                'dashboard': '📊 Dashboard',
                'products': '📦 Quản lý sản phẩm',
                'orders': '📋 Quản lý đơn hàng',
                'customers': '👥 Quản lý khách hàng',
                'reports': '📈 Báo cáo thống kê',
                'settings': '⚙️ Cài đặt'
            };
            
            document.getElementById('navbar-title').textContent = titleMap[tabName] || tabName;
            
            // Close sidebar on mobile
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth < 768) {
                sidebar.classList.remove('active');
            }
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggle-btn');
            
            if (window.innerWidth < 768) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
</body>
</html>
