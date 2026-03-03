@extends('layouts.app')

@section('title', 'Admin Reports Dashboard')

@section('content')
<!-- DEBUG: Session Success Message -->
@if (session('success'))
    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px 20px; margin: 15px; border-radius: 8px; font-weight: 500;">
        ✓ {{ session('success') }}
    </div>
@endif

<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 style="color: #3b82f6; font-weight: bold;">
                <i class="fas fa-chart-line"></i> Dashboard Quản Trị
            </h1>
            @php
                $userName = auth()->user()?->name ?? 'Admin';
            @endphp
            <p class="text-muted">Chào mừng, {{ $userName }} 👋</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stats-card" style="border-left: 4px solid #48bb78; cursor: pointer;" data-chart-type="revenue">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stats-label">Tổng Doanh Thu</p>
                            <h2 class="stats-value" style="color: #48bb78;">₫{{ number_format($stats['total_revenue'], 0, ',', '.') }}</h2>
                        </div>
                        <div class="stats-icon" style="background: #c6f6d5;">
                            <i class="fas fa-money-bill-wave" style="color: #48bb78;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card stats-card" style="border-left: 4px solid #4299e1; cursor: pointer;" data-chart-type="orders">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stats-label">Tổng Đơn Hàng</p>
                            <h2 class="stats-value" style="color: #4299e1;">{{ number_format($stats['total_orders']) }}</h2>
                        </div>
                        <div class="stats-icon" style="background: #bee3f8;">
                            <i class="fas fa-shopping-bag" style="color: #4299e1;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card stats-card" style="border-left: 4px solid #ed8936; cursor: pointer;" data-chart-type="products">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stats-label">Sản Phẩm</p>
                            <h2 class="stats-value" style="color: #ed8936;">{{ number_format($stats['total_products']) }}</h2>
                        </div>
                        <div class="stats-icon" style="background: #fbd38d;">
                            <i class="fas fa-box" style="color: #ed8936;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card stats-card" style="border-left: 4px solid #9f7aea; cursor: pointer;" data-chart-type="users">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stats-label">Người Dùng</p>
                            <h2 class="stats-value" style="color: #9f7aea;">{{ number_format($stats['total_users']) }}</h2>
                        </div>
                        <div class="stats-icon" style="background: #e9d8fd;">
                            <i class="fas fa-users" style="color: #9f7aea;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Detail Modal -->
    <div id="securityModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 8px; width: 90%; max-width: 700px; max-height: 80vh; overflow-y: auto; box-shadow: 0 5px 25px rgba(0,0,0,0.3);">
            <div style="padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white;">
                <h3 id="securityModalTitle" style="margin: 0; color: #2d3748;">Chi Tiết</h3>
                <button onclick="closeSecurityModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">✕</button>
            </div>

            <!-- Alerts Content -->
            <div id="security-modal-alerts" data-security-modal style="padding: 20px; display: none;">
                <div class="alert alert-info">
                    <strong>Tổng cảnh báo:</strong> {{ count($securityAlerts) }}
                </div>
                <table class="table table-sm">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th>Loại Cảnh Báo</th>
                            <th>Mô Tả</th>
                            <th>Số Lần</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($securityAlerts as $alert)
                        <tr>
                            <td><strong>{{ $alert['type'] }}</strong></td>
                            <td>{{ $alert['description'] }}</td>
                            <td><span class="badge bg-danger">{{ $alert['count'] }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Không có cảnh báo</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Anomalies Content -->
            <div id="security-modal-anomalies" data-security-modal style="padding: 20px; display: none;">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Tổng Phát Hiện (24h)</h6>
                                <h2 style="color: #f6ad55;">{{ $securityMetrics['login_anomalies'] ?? 0 }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <h6>Login Đáng Ngờ Gần Đây:</h6>
                <div class="list-group">
                    @forelse($securityMetrics['recent_suspicious'] as $suspicious)
                    <div class="list-group-item" style="border-left: 4px solid #f6ad55;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $suspicious->user->name ?? 'Unknown' }}</h6>
                                <small class="text-muted">
                                    🔍 IP: {{ $suspicious->ip_address }}<br>
                                    🌍 Quốc gia: {{ $suspicious->country ?? 'Unknown' }}<br>
                                    ⚠️ Risk Level: <span class="badge bg-{{ $suspicious->risk_level == 'high' ? 'danger' : ($suspicious->risk_level == 'medium' ? 'warning' : 'info') }}">{{ $suspicious->risk_level }}</span>
                                </small>
                            </div>
                            <span class="badge bg-warning">{{ $suspicious->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">Không có login đáng ngờ</p>
                    @endforelse
                </div>
            </div>

            <!-- Failed Attempts Content -->
            <div id="security-modal-failed" data-security-modal style="padding: 20px; display: none;">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Lần Thất Bại (24h)</h6>
                                <h2 style="color: #805ad5;">{{ $securityMetrics['failed_attempts'] ?? 0 }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Tài Khoản Bị Khóa</h6>
                                <h2 style="color: #c53030;">{{ $securityMetrics['locked_accounts'] ?? 0 }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <h6>Lần Đăng Nhập Thất Bại Gần Đây:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th>Email</th>
                                <th>IP Address</th>
                                <th>Thời Gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $failedLogins = \App\Models\LoginAttempt::where('success', false)
                                    ->where('attempted_at', '>=', now()->subHours(24))
                                    ->with('user')
                                    ->orderByDesc('attempted_at')
                                    ->limit(10)
                                    ->get();
                            @endphp
                            @forelse($failedLogins as $attempt)
                            <tr>
                                <td>{{ $attempt->user->email ?? 'Unknown' }}</td>
                                <td>{{ $attempt->ip_address }}</td>
                                <td><small>{{ $attempt->attempted_at->diffForHumans() }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Không có lỗi đăng nhập</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Concurrent Logins Content -->
            <div id="security-modal-concurrent" data-security-modal style="padding: 20px; display: none;">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Lần Phát Hiện (24h)</h6>
                                <h2 style="color: #3182ce;">{{ $securityMetrics['concurrent_logins'] ?? 0 }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Tài Khoản Bị Ảnh Hưởng</h6>
                                <h2 style="color: #0284c7;">{{ $securityMetrics['users_with_concurrent'] ?? 0 }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <h6>Các Trường Hợp Đồng Thời Gần Đây:</h6>
                <div class="list-group">
                    @forelse($securityMetrics['recent_concurrent'] as $concurrent)
                    <div class="list-group-item" style="border-left: 4px solid #3182ce;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $concurrent->user->name ?? 'Unknown' }} ({{ $concurrent->user->email ?? '' }})</h6>
                                <small class="text-muted">
                                    📍 IP 1: {{ $concurrent->primary_ip_address }}<br>
                                    📍 IP 2: {{ $concurrent->secondary_ip_address }}<br>
                                    ✓ Trạng thái: <span class="badge bg-danger">{{ ucfirst($concurrent->status) }}</span>
                                </small>
                            </div>
                            <span class="badge bg-info">{{ $concurrent->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">Không có login đồng thời</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Modal -->
    <div id="chartModal" class="chart-modal">
        <div class="chart-modal-content">
            <div class="chart-modal-header">
                <h2 id="chartTitle">Biểu Đồ</h2>
                <button class="chart-modal-close" id="closeChartBtn">&times;</button>
            </div>
            <div style="position: relative; height: 400px;">
                <canvas id="dashboardChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Đơn Hàng Gần Đây
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($recentOrders) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th>#ID</th>
                                        <th>Khách Hàng</th>
                                        <th>Tổng Tiền</th>
                                        <th>Trạng Thái</th>
                                        <th>Ngày</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td><strong>#{{ $order['id'] }}</strong></td>
                                            <td>{{ $order['user'] }}</td>
                                            <td>₫{{ number_format($order['total'], 0, ',', '.') }}</td>
                                            <td>
                                                @if($order['status'] == 'pending')
                                                    <span class="badge bg-warning">Chờ xử lý</span>
                                                @elseif($order['status'] == 'confirmed')
                                                    <span class="badge bg-info">Xác nhận</span>
                                                @elseif($order['status'] == 'shipped')
                                                    <span class="badge bg-primary">Đang giao</span>
                                                @elseif($order['status'] == 'completed')
                                                    <span class="badge bg-success">Đã giao</span>
                                                @else
                                                    <span class="badge bg-danger">Hủy</span>
                                                @endif
                                            </td>
                                            <td>{{ $order['date'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Chưa có đơn hàng</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-star"></i> Sản Phẩm Nổi Bật
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($topProducts) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($topProducts as $product)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $product['name'] }}</h6>
                                        <small class="text-muted">{{ $product['sales'] }} sales</small>
                                    </div>
                                    <span class="badge bg-warning">💰 ${{ number_format($product['revenue'], 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Chưa có sản phẩm</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Security Monitoring Section -->
    <div class="row mt-5 mb-4">
        <div class="col-12">
            <h5 style="color: #e53e3e; font-weight: bold;">
                <i class="fas fa-shield-alt"></i> Bảo Mật & Giám Sát
            </h5>
        </div>
    </div>

    <!-- Security Alerts Cards -->
    <div class="row mb-4">
        <!-- Security Alerts -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="cursor: pointer;" onclick="openSecurityModal('alerts')">
                <div class="card-header" style="background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-circle"></i> Cảnh Báo Bảo Mật (Chi tiết)
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($securityAlerts) && count($securityAlerts) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($securityAlerts as $alert)
                                <div class="list-group-item px-0 py-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $alert['type'] ?? 'Security Alert' }}</h6>
                                            <small class="text-muted">{{ $alert['description'] ?? 'Alert' }}</small>
                                        </div>
                                        <span class="badge bg-danger">{{ $alert['count'] ?? 1 }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>Không có cảnh báo bảo mật</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Login Anomalies -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="cursor: pointer;" onclick="openSecurityModal('anomalies')">
                <div class="card-header" style="background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Login Bất Thường (Chi tiết)
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($securityMetrics) && isset($securityMetrics['login_anomalies']))
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 style="color: #f6ad55; font-weight: bold;">
                                    {{ $securityMetrics['login_anomalies'] ?? 0 }}
                                </h4>
                                <small class="text-muted">Phát hiện bất thường</small>
                            </div>
                            <i class="fas fa-user-slash" style="font-size: 2.5rem; color: #fbd38d;"></i>
                        </div>
                        <hr>
                        <small class="text-muted">Cập nhật lần cuối: {{ now()->format('H:i:s, d/m/Y') }}</small>
                    @else
                        <div class="alert alert-info">
                            <small>Không có dữ liệu bất thường</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Failed Attempts & Concurrent Logins -->
    <div class="row mb-4">
        <!-- Failed Attempts -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="cursor: pointer;" onclick="openSecurityModal('failed')">
                <div class="card-header" style="background: linear-gradient(135deg, #805ad5 0%, #6b46c1 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-times-circle"></i> Lần Đăng Nhập Thất Bại (Chi tiết)
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($securityMetrics) && isset($securityMetrics['failed_attempts']))
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 style="color: #805ad5; font-weight: bold;">
                                    {{ $securityMetrics['failed_attempts'] ?? 0 }}
                                </h4>
                                <small class="text-muted">Trong 24 giờ qua</small>
                            </div>
                            <i class="fas fa-lock" style="font-size: 2.5rem; color: #d6bcfa;"></i>
                        </div>
                        <hr>
                        @if(isset($securityMetrics['locked_accounts']))
                            <small class="text-danger">
                                <strong>{{ $securityMetrics['locked_accounts'] ?? 0 }}</strong> tài khoản bị khóa
                            </small>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <small>Không có dữ liệu lỗi đăng nhập</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Concurrent Logins -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="cursor: pointer;" onclick="openSecurityModal('concurrent')">
                <div class="card-header" style="background: linear-gradient(135deg, #3182ce 0%, #2c5aa0 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-network-wired"></i> Đăng Nhập Đồng Thời (Chi tiết)
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($securityMetrics) && isset($securityMetrics['concurrent_logins']))
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 style="color: #3182ce; font-weight: bold;">
                                    {{ $securityMetrics['concurrent_logins'] ?? 0 }}
                                </h4>
                                <small class="text-muted">Phát hiện trong 24 giờ</small>
                            </div>
                            <i class="fas fa-laptop-house" style="font-size: 2.5rem; color: #bee3f8;"></i>
                        </div>
                        <hr>
                        @if(isset($securityMetrics['users_with_concurrent']))
                            <small class="text-muted">
                                <strong>{{ $securityMetrics['users_with_concurrent'] ?? 0 }}</strong> tài khoản bị ảnh hưởng
                            </small>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <small>Không có login đồng thời</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Management Links -->
    <div class="row mt-4 mb-4">
        <div class="col-12">
            <h5 style="color: #667eea; font-weight: bold;">Quản Lý Nhanh</h5>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.products') }}" class="card text-decoration-none admin-link-card" style="border-left: 4px solid #ed8936;">
                <div class="card-body text-center">
                    <i class="fas fa-box" style="font-size: 2rem; color: #ed8936;"></i>
                    <h6 class="mt-3">Quản Lý Sản Phẩm</h6>
                    <p class="text-muted mb-0">{{ $stats['total_products'] }} sản phẩm</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.categories') }}" class="card text-decoration-none admin-link-card" style="border-left: 4px solid #9f7aea;">
                <div class="card-body text-center">
                    <i class="fas fa-list" style="font-size: 2rem; color: #9f7aea;"></i>
                    <h6 class="mt-3">Quản Lý Danh Mục</h6>
                    <p class="text-muted mb-0">Danh mục sản phẩm</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.orders') }}" class="card text-decoration-none admin-link-card" style="border-left: 4px solid #4299e1;">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-bag" style="font-size: 2rem; color: #4299e1;"></i>
                    <h6 class="mt-3">Quản Lý Đơn Hàng</h6>
                    <p class="text-muted mb-0">{{ $stats['total_orders'] }} đơn hàng</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.users') }}" class="card text-decoration-none admin-link-card" style="border-left: 4px solid #48bb78;">
                <div class="card-body text-center">
                    <i class="fas fa-users" style="font-size: 2rem; color: #48bb78;"></i>
                    <h6 class="mt-3">Quản Lý Người Dùng</h6>
                    <p class="text-muted mb-0">{{ $stats['total_users'] }} người dùng</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Security Management Links -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 style="color: #e53e3e; font-weight: bold;">🔐 Quản Lý Bảo Mật</h5>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.security-settings.index') }}" class="card text-decoration-none admin-link-card" style="border-left: 4px solid #e53e3e;">
                <div class="card-body text-center">
                    <i class="fas fa-lock" style="font-size: 2rem; color: #e53e3e;"></i>
                    <h6 class="mt-3">Cài Đặt Bảo Mật</h6>
                    <p class="text-muted mb-0">Security config</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.security-settings.blocked-ips') }}" class="card text-decoration-none admin-link-card" style="border-left: 4px solid #f6ad55;">
                <div class="card-body text-center">
                    <i class="fas fa-ban" style="font-size: 2rem; color: #f6ad55;"></i>
                    <h6 class="mt-3">IP Bị Chặn</h6>
                    <p class="text-muted mb-0">{{ isset($securityMetrics['blocked_ips']) ? count($securityMetrics['blocked_ips']) : 0 }} IP</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.security-monitoring') }}" class="card text-decoration-none admin-link-card" style="border-left: 4px solid #805ad5;">
                <div class="card-body text-center">
                    <i class="fas fa-history" style="font-size: 2rem; color: #805ad5;"></i>
                    <h6 class="mt-3">Security Logs</h6>
                    <p class="text-muted mb-0">Lịch sử bảo mật</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.anomalies.dashboard') }}" class="card text-decoration-none admin-link-card" style="border-left: 4px solid #3182ce;">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #3182ce;"></i>
                    <h6 class="mt-3">Phát Hiện Bất Thường</h6>
                    <p class="text-muted mb-0">{{ $securityMetrics['login_anomalies'] ?? 0 }} anomaly</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Back Button -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="{{ route('home') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay Lại Trang Chủ
            </a>
        </div>
    </div>
</div>

<style>
    .stats-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }

    .stats-label {
        color: #718096;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .stats-value {
        font-size: 1.8rem;
        font-weight: bold;
        margin: 0;
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .admin-link-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .admin-link-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }

    .admin-link-card h6 {
        color: #2d3748;
        font-weight: 600;
    }

    /* Chart Modal Styles */
    .chart-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .chart-modal.active {
        display: flex;
    }

    .chart-modal-content {
        background: white;
        border-radius: 12px;
        padding: 30px;
        width: 90%;
        max-width: 800px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        position: relative;
        max-height: 90vh;
        overflow-y: auto;
    }

    .chart-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .chart-modal-header h2 {
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    .chart-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #a0aec0;
        transition: color 0.2s;
    }

    .chart-modal-close:hover {
        color: #2d3748;
    }

    .stats-card {
        transition: all 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
</style>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    let chartInstance = null;
    
    console.log('📌 Script loaded');

    // Prepare sales chart data from server
    const salesChartData = @json($salesChart);

    // Attach event listeners with a small delay
    setTimeout(function() {
        console.log('🔍 Looking for cards...');
        
        const cards = document.querySelectorAll('[data-chart-type]');
        console.log('✅ Found', cards.length, 'cards');
        
        cards.forEach((card, i) => {
            const type = card.getAttribute('data-chart-type');
            console.log('  Card', i, ':', type);
            
            card.addEventListener('click', function(e) {
                console.log('🎯 Clicked:', type);
                e.preventDefault();
                e.stopPropagation();
                showChart(type);
            });
        });
        
        // Setup close button
        const closeBtn = document.getElementById('closeChartBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeChart);
            console.log('✅ Close button attached');
        }
        
        // Setup modal background click
        const modal = document.getElementById('chartModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeChart();
                }
            });
            console.log('✅ Modal click handler attached');
        }
    }, 100);

    function showChart(type) {
        console.log('📊 showChart called:', type);
        
        const modal = document.getElementById('chartModal');
        const canvas = document.getElementById('dashboardChart');
        const titleEl = document.getElementById('chartTitle');
        
        if (!modal || !canvas) {
            console.error('❌ Modal or Canvas missing');
            return;
        }
        
        const ctx = canvas.getContext('2d');

        // Destroy previous chart if exists
        if (chartInstance) {
            chartInstance.destroy();
        }

        let chartData = {};

        if (type === 'revenue') {
            titleEl.textContent = '📊 Biểu Đồ Doanh Thu (7 Ngày)';
            chartData = {
                type: 'line',
                data: {
                    labels: Object.keys(salesChartData),
                    datasets: [{
                        label: 'Doanh Thu (₫)',
                        data: Object.values(salesChartData),
                        borderColor: '#48bb78',
                        backgroundColor: 'rgba(72, 187, 120, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBorderColor: '#48bb78',
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₫' + value.toLocaleString('vi-VN');
                                }
                            }
                        }
                    }
                }
            };
        } else if (type === 'orders') {
            titleEl.textContent = '📦 Biểu Đồ Đơn Hàng';
            chartData = {
                type: 'bar',
                data: {
                    labels: ['Chờ xử lý', 'Xác nhận', 'Đang giao', 'Đã giao'],
                    datasets: [{
                        label: 'Số Đơn',
                        data: [
                            {{ $stats['pending_orders'] }}, 
                            {{ $stats['completed_orders'] }}, 
                            {{ $stats['total_orders'] - $stats['pending_orders'] - $stats['completed_orders'] }}, 
                            {{ $stats['completed_orders'] }}
                        ],
                        backgroundColor: [
                            '#f6ad55', '#48bb78', '#4299e1', '#9f7aea'
                        ],
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true }
                    }
                }
            };
        } else if (type === 'products') {
            titleEl.textContent = '📦 Phân Bố Kho';
            chartData = {
                type: 'doughnut',
                data: {
                    labels: ['Còn Hàng', 'Hết Hàng', 'Sắp Hết'],
                    datasets: [{
                        data: [
                            {{ $stats['active_products'] }}, 
                            {{ $stats['total_products'] - $stats['active_products'] - $stats['low_stock'] }}, 
                            {{ $stats['low_stock'] }}
                        ],
                        backgroundColor: [
                            '#48bb78', '#ed8936', '#f6ad55'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            };
        } else if (type === 'users') {
            titleEl.textContent = '👥 Thống Kê Người Dùng';
            chartData = {
                type: 'bar',
                data: {
                    labels: ['Tổng', 'Đang Hoạt Động', 'Người Mới (Tháng)', 'Người Mới (Hôm)'],
                    datasets: [{
                        label: 'Số Người',
                        data: [
                            {{ $stats['total_users'] }},
                            {{ $stats['active_users'] }},
                            {{ $stats['new_users_month'] }},
                            {{ $stats['new_users_today'] }}
                        ],
                        backgroundColor: '#9f7aea',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true }
                    }
                }
            };
        }

        chartInstance = new Chart(ctx, chartData);
        console.log('✨ Chart instance created');
        modal.classList.add('active');
        console.log('✨ Modal shown');
    }

    function closeChart() {
        console.log('🚪 Closing chart');
        const modal = document.getElementById('chartModal');
        if (modal) {
            modal.classList.remove('active');
        }
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }
    }

    function openSecurityModal(type) {
        const modal = document.getElementById('securityModal');
        const titleEl = document.getElementById('securityModalTitle');
        
        // Hide all modals
        document.querySelectorAll('[data-security-modal]').forEach(el => {
            el.style.display = 'none';
        });
        
        // Show selected modal and update title
        const selectedModal = document.getElementById(`security-modal-${type}`);
        if (selectedModal) {
            selectedModal.style.display = 'block';
            
            // Update title based on type
            const titles = {
                'alerts': '🚨 Chi Tiết Cảnh Báo Bảo Mật',
                'anomalies': '👥 Chi Tiết Login Bất Thường',
                'failed': '❌ Chi Tiết Lần Đăng Nhập Thất Bại',
                'concurrent': '🌐 Chi Tiết Đăng Nhập Đồng Thời'
            };
            titleEl.textContent = titles[type] || 'Chi Tiết';
        }
        
        modal.style.display = 'flex';
    }

    function closeSecurityModal() {
        const modal = document.getElementById('securityModal');
        modal.style.display = 'none';
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('securityModal');
        if (event.target === modal) {
            closeSecurityModal();
        }
    });

</script>

@endsection

