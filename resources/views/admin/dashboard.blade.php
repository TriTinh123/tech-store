@extends('layouts.app')

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
            <p class="text-muted">Chào mừng, {{ Auth::user()->name }} 👋</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stats-card" style="border-left: 4px solid #48bb78; cursor: pointer;" data-chart-type="revenue">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stats-label">Tổng Doanh Thu</p>
                            <h2 class="stats-value" style="color: #48bb78;">₫{{ number_format($totalRevenue, 0, ',', '.') }}</h2>
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
                            <h2 class="stats-value" style="color: #4299e1;">{{ $totalOrders }}</h2>
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
                            <h2 class="stats-value" style="color: #ed8936;">{{ $totalProducts }}</h2>
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
                            <h2 class="stats-value" style="color: #9f7aea;">{{ $totalUsers }}</h2>
                        </div>
                        <div class="stats-icon" style="background: #e9d8fd;">
                            <i class="fas fa-users" style="color: #9f7aea;"></i>
                        </div>
                    </div>
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
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th>#ID</th>
                                        <th>Khách Hàng</th>
                                        <th>Tổng Tiền</th>
                                        <th>Trạng Thái</th>
                                        <th>Ngày</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td><strong>#{{ $order->id }}</strong></td>
                                            <td>{{ $order->user->name ?? 'Guest' }}</td>
                                            <td>₫{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                            <td>
                                                @if($order->status == 'pending')
                                                    <span class="badge bg-warning">Chờ xử lý</span>
                                                @elseif($order->status == 'confirmed')
                                                    <span class="badge bg-info">Xác nhận</span>
                                                @elseif($order->status == 'shipped')
                                                    <span class="badge bg-primary">Đang giao</span>
                                                @elseif($order->status == 'delivered')
                                                    <span class="badge bg-success">Đã giao</span>
                                                @else
                                                    <span class="badge bg-danger">Hủy</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
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
                    @if($topProducts->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($topProducts as $product)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <small class="text-muted">₫{{ number_format($product->price, 0, ',', '.') }}</small>
                                    </div>
                                    <span class="badge bg-warning">⭐ {{ $product->rating ?? 0 }}</span>
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

    <!-- Management Links -->
    <div class="row mt-4">
        <div class="col-12">
            <h5 style="color: #667eea; font-weight: bold;">Quản Lý Nhanh</h5>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.products') }}" class="card text-decoration-none admin-link-card" style="border-left: 4px solid #ed8936;">
                <div class="card-body text-center">
                    <i class="fas fa-box" style="font-size: 2rem; color: #ed8936;"></i>
                    <h6 class="mt-3">Quản Lý Sản Phẩm</h6>
                    <p class="text-muted mb-0">{{ $totalProducts }} sản phẩm</p>
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
                    <p class="text-muted mb-0">{{ $totalOrders }} đơn hàng</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.users') }}" class="card text-decoration-none admin-link-card" style="border-left: 4px solid #48bb78;">
                <div class="card-body text-center">
                    <i class="fas fa-users" style="font-size: 2rem; color: #48bb78;"></i>
                    <h6 class="mt-3">Quản Lý Người Dùng</h6>
                    <p class="text-muted mb-0">{{ $totalUsers }} người dùng</p>
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
            titleEl.textContent = '📊 Biểu Đồ Doanh Thu';
            chartData = {
                type: 'line',
                data: {
                    labels: ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4', 'Tuần 5'],
                    datasets: [{
                        label: 'Doanh Thu (₫)',
                        data: [5000000, 7500000, 6200000, 8900000, 11200000],
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
                    labels: ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN'],
                    datasets: [{
                        label: 'Số Đơn Hàng',
                        data: [12, 15, 10, 18, 14, 22, 16],
                        backgroundColor: [
                            '#4299e1', '#3182ce', '#2c5aa0', '#2c5aa0', '#2c5aa0', '#3182ce', '#4299e1'
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
            titleEl.textContent = '📦 Biểu Đồ Sản Phẩm';
            chartData = {
                type: 'doughnut',
                data: {
                    labels: ['Mouse', 'Keyboard', 'Headset', 'Monitor', 'Khác'],
                    datasets: [{
                        data: [12, 8, 6, 5, 4],
                        backgroundColor: [
                            '#ed8936', '#ecc94b', '#48bb78', '#4299e1', '#b794f6'
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
            titleEl.textContent = '👥 Biểu Đồ Người Dùng';
            chartData = {
                type: 'line',
                data: {
                    labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                    datasets: [{
                        label: 'Người Dùng Mới',
                        data: [5, 8, 12, 15, 18, 14, 16],
                        borderColor: '#9f7aea',
                        backgroundColor: 'rgba(159, 122, 234, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#9f7aea'
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
</script>

@endsection
