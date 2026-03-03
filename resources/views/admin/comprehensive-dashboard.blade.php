@extends('layouts.app')

@section('title', 'Comprehensive Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">📊 Tổng Hợp Bảng Điều Khiển</h1>
                <p class="text-muted small">Giám sát toàn bộ hệ thống · Thời gian thực · {{ now()->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">🔄 Làm mới</button>
                <a href="#" class="btn btn-sm btn-outline-primary">📥 Xuất báo cáo</a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row 1 -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">👥 Người Dùng</p>
                            <h3 class="mb-0 text-primary">{{ number_format($stats['total_users']) }}</h3>
                            <small class="text-success">+{{ $stats['new_users_today'] }} hôm nay</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">📦 Đơn Hàng</p>
                            <h3 class="mb-0 text-success">{{ number_format($stats['total_orders']) }}</h3>
                            <small class="text-primary">{{ $stats['orders_today'] }} hôm nay</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">💰 Doanh Thu</p>
                            <h3 class="mb-0 text-warning">{{ number_format($stats['total_revenue'], 0) }}đ</h3>
                            <small class="text-info">{{ number_format($stats['revenue_today'], 0) }}đ hôm nay</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">🎁 Sản Phẩm</p>
                            <h3 class="mb-0 text-danger">{{ number_format($stats['total_products']) }}</h3>
                            <small class="text-warning">{{ $stats['low_stock'] }} tồn kho thấp</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row 2 - Security Focus -->
    <div class="row mb-4">
        <div class="col-md-2 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-info">{{ $stats['active_sessions'] }}</h4>
                    <small class="text-muted">Phiên Hoạt Động</small>
                </div>
            </div>
        </div>

        <div class="col-md-2 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-warning">{{ $stats['concurrent_logins'] }}</h4>
                    <small class="text-muted">Đăng Nhập Đồng Thời</small>
                </div>
            </div>
        </div>

        <div class="col-md-2 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-danger">{{ $stats['suspicious_activities'] }}</h4>
                    <small class="text-muted">Hoạt Động Đáng Ngờ</small>
                </div>
            </div>
        </div>

        <div class="col-md-2 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-danger">{{ $stats['blocked_ips'] }}</h4>
                    <small class="text-muted">IP Bị Chặn</small>
                </div>
            </div>
        </div>

        <div class="col-md-2 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-warning">{{ $alerts['total_unread'] }}</h4>
                    <small class="text-muted">Cảnh Báo Chưa Đọc</small>
                </div>
            </div>
        </div>

        <div class="col-md-2 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-success">{{ $stats['active_users'] }}</h4>
                    <small class="text-muted">Người Dùng Hoạt Động</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content 3-column layout -->
    <div class="row mb-4">
        <!-- Left Column - 50% -->
        <div class="col-lg-6">
            <!-- Recent Orders -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">📋 Đơn Hàng Gần Đây</h6>
                    <a href="/admin/orders" class="btn btn-sm btn-link">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách Hàng</th>
                                        <th>Tổng Tiền</th>
                                        <th>Trạng Thái</th>
                                        <th>Ngày</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td><strong>#{{ $order->id }}</strong></td>
                                            <td>{{ $order->user->name ?? 'Guest' }}</td>
                                            <td>{{ number_format($order->total_amount, 0) }}đ</td>
                                            <td>
                                                @switch($order->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">⏳ Chờ</span>
                                                        @break
                                                    @case('processing')
                                                        <span class="badge bg-info">⚙️ Xử Lý</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-success">✓ Hoàn Thành</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-danger">✕ Hủy</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $order->status }}</span>
                                                @endswitch
                                            </td>
                                            <td><small class="text-muted">{{ $order->created_at->diffForHumans() }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center p-4">Chưa có đơn hàng</p>
                    @endif
                </div>
            </div>

            <!-- Activity Logs -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">📝 Nhật Ký Hoạt Động</h6>
                    <a href="/admin/logs/system" class="btn btn-sm btn-link">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    @if($activityLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Người Dùng</th>
                                        <th>Hành Động</th>
                                        <th>Mô Tả</th>
                                        <th>Thời Gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activityLogs as $log)
                                        <tr>
                                            <td><small>{{ $log->user->name ?? 'System' }}</small></td>
                                            <td><small><strong>{{ $log->action }}</strong></small></td>
                                            <td><small class="text-muted">{{ Str::limit($log->description, 40) }}</small></td>
                                            <td><small class="text-muted">{{ $log->created_at->diffForHumans() }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center p-4">Chưa có nhật ký</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - 50% -->
        <div class="col-lg-6">
            <!-- Alerts & Warnings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">🔔 Cảnh Báo & Thông Báo</h6>
                    <a href="/notifications" class="btn btn-sm btn-link">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4 text-center">
                            <p class="text-muted small">🔴 Nghiêm Trọng</p>
                            <h4 class="text-danger">{{ $alerts['critical_alerts'] }}</h4>
                        </div>
                        <div class="col-4 text-center">
                            <p class="text-muted small">🟠 Cảnh Báo</p>
                            <h4 class="text-warning">{{ $alerts['warning_alerts'] }}</h4>
                        </div>
                        <div class="col-4 text-center">
                            <p class="text-muted small">🔵 Thông Tin</p>
                            <h4 class="text-info">{{ $alerts['info_alerts'] }}</h4>
                        </div>
                    </div>

                    @if($recentAlerts->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentAlerts->take(5) as $alert)
                                <a href="#" class="list-group-item list-group-item-action py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <small class="text-muted">{{ $alert->user->name ?? 'System' }}</small>
                                            <p class="mb-1"><small>{{ Str::limit($alert->message, 60) }}</small></p>
                                            <small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small>
                                        </div>
                                        <span class="badge bg-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'warning' ? 'warning' : 'info') }}">
                                            {{ str_replace('_', ' ', ucfirst($alert->type)) }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">Chưa có cảnh báo</p>
                    @endif
                </div>
            </div>

            <!-- Top Locations/Cities -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">🌍 Thành Phố Hàng Đầu</h6>
                </div>
                <div class="card-body">
                    @if($topCities->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topCities as $city)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <strong>{{ $city->location ?? 'Unknown' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $city->country ?? 'N/A' }}</small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $city->session_count }} sessions</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">Không có dữ liệu vị trí</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row - Security & Products -->
    <div class="row mb-4">
        <!-- Top Products -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">⭐ Sản Phẩm Bán Chạy</h6>
                    <a href="/admin/products" class="btn btn-sm btn-link">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    @if($topProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản Phẩm</th>
                                        <th>Số Lần Mua</th>
                                        <th>Tổng Số Lượng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProducts as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? 'Unknown' }}</td>
                                            <td><span class="badge bg-success">{{ $item->order_count }}</span></td>
                                            <td><strong>{{ $item->total_qty }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center p-4">Chưa có dữ liệu bán hàng</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Security Threats -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">🚨 Mối Đe Dọa Bảo Mật</h6>
                    <a href="/admin/security-dashboard" class="btn btn-sm btn-link">Chi tiết</a>
                </div>
                <div class="card-body">
                    @if(count($securityThreats['recent_concurrent']) > 0 || count($securityThreats['recent_suspicious']) > 0)
                        <h6 class="text-danger mb-2">🔴 Đăng nhập đồng thời gần đây:</h6>
                        @forelse($securityThreats['recent_concurrent'] as $threat)
                            <small class="d-block text-muted">{{ $threat->user->name ?? 'Unknown' }} - {{ $threat->created_at->diffForHumans() }}</small>
                        @empty
                            <small class="text-muted">Không có đăng nhập đồng thời</small>
                        @endforelse

                        <hr>

                        <h6 class="text-warning mb-2">⚠️ Hoạt động đáng ngờ:</h6>
                        @forelse($securityThreats['recent_suspicious'] as $threat)
                            <small class="d-block text-muted">{{ $threat->user->name ?? 'Unknown' }} - Risk: <span class="badge bg-warning">{{ $threat->risk_score ?? 0 }}%</span></small>
                        @empty
                            <small class="text-muted">Không có hoạt động đáng ngờ</small>
                        @endforelse

                        <hr>

                        <h6 class="text-danger mb-2">🚫 IP bị chặn:</h6>
                        @forelse($securityThreats['blocked_ips'] as $ip)
                            <small class="d-block text-muted">{{ $ip->ip_address ?? 'Unknown' }} - {{ $ip->created_at->diffForHumans() }}</small>
                        @empty
                            <small class="text-muted">Không có IP bị chặn</small>
                        @endforelse
                    @else
                        <p class="text-success text-center">✅ Hệ thống hoạt động bình thường</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Third Row - Geographic & Login Stats -->
    <div class="row mb-4">
        <!-- Cities Statistics -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">📍 Thống Kê Thành Phố</h6>
                </div>
                <div class="card-body p-0">
                    @if($cityStats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Thành Phố</th>
                                        <th>Quốc Gia</th>
                                        <th>Người Dùng Unik</th>
                                        <th>Phiên</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cityStats as $city)
                                        <tr>
                                            <td><strong>{{ $city->location ?? 'Unknown' }}</strong></td>
                                            <td><small>{{ $city->country ?? 'N/A' }}</small></td>
                                            <td><span class="badge bg-primary">{{ $city->unique_users }}</span></td>
                                            <td><span class="badge bg-info">{{ $city->total_sessions }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center p-4">Không có dữ liệu</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Login Statistics -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">🔐 Thống Kê Đăng Nhập</h6>
                    <a href="/admin/logs/login" class="btn btn-sm btn-link">Xem log</a>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <p class="text-muted small">Hôm Nay</p>
                            <h5 class="text-success">{{ $loginStats['today_logins'] }}</h5>
                            <small class="text-danger">{{ $loginStats['today_failed'] }} thất bại</small>
                        </div>
                        <div class="col-6">
                            <p class="text-muted small">30 Ngày Qua</p>
                            <h5 class="text-success">{{ $loginStats['month_logins'] }}</h5>
                            <small class="text-danger">{{ $loginStats['month_failed'] }} thất bại</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Status -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">📊 Trạng Thái Đơn Hàng</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        @foreach($orderStatusCount as $status => $count)
                            <li class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>{{ ucfirst($status) }}</small>
                                    <span class="badge bg-secondary">{{ $count }}</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" style="width: {{ ($count / $stats['total_orders']) * 100 }}%"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health & Footer -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">🔧 Trạng Thái Hệ Thống</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h5>{{ $systemHealth['database_check'] ? '✅' : '❌' }}</h5>
                            <small>Cơ Sở Dữ Liệu</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h5>{{ $systemHealth['cache_check'] ? '✅' : '❌' }}</h5>
                            <small>Bộ Nhớ Đệm</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h5>{{ $systemHealth['queue_check'] ? '✅' : '❌' }}</h5>
                            <small>Hàng Đợi</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h5>{{ $systemHealth['storage_check'] ? '✅' : '❌' }}</h5>
                            <small>Lưu Trữ</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-opacity-10 {
        opacity: 0.1;
    }
</style>
@endsection
