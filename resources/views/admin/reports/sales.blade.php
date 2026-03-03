@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="admin-reports-container py-5">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-chart-bar"></i> Sales Report</h1>
                <small class="text-muted">Comprehensive sales analytics and metrics</small>
            </div>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <button class="btn btn-outline-primary" onclick="exportReport('csv')">
                    <i class="fas fa-download"></i> Export CSV
                </button>
                <button class="btn btn-outline-primary" onclick="exportReport('pdf')">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </div>
        </div>

        <!-- Period Selector -->
        <div class="mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Time Period</label>
                            <select class="form-select" name="period" onchange="this.form.submit()">
                                <option value="7" {{ $period == 7 ? 'selected' : '' }}>Last 7 Days</option>
                                <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 Days</option>
                                <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last 90 Days</option>
                                <option value="365" {{ $period == 365 ? 'selected' : '' }}>Last Year</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-5">
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm metrics-card">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Total Revenue</small>
                        <h3 class="h2 mt-2 mb-0">${{ number_format($totalRevenue, 2) }}</h3>
                        <div class="mt-3">
                            <small class="text-success"><i class="fas fa-arrow-up"></i> +12.5% vs period</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm metrics-card">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Total Orders</small>
                        <h3 class="h2 mt-2 mb-0">{{ number_format($totalOrders) }}</h3>
                        <div class="mt-3">
                            <small class="text-success"><i class="fas fa-arrow-up"></i> +8.3% vs period</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm metrics-card">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Average Order Value</small>
                        <h3 class="h2 mt-2 mb-0">${{ number_format($averageOrderValue, 2) }}</h3>
                        <div class="mt-3">
                            <small class="text-danger"><i class="fas fa-arrow-down"></i> -3.2% vs period</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm metrics-card">
                    <div class="card-body">
                        <small class="text-muted text-uppercase">Conversion Rate</small>
                        <h3 class="h2 mt-2 mb-0">{{ $conversionRate }}%</h3>
                        <div class="mt-3">
                            <small class="text-info"><i class="fas fa-equals"></i> Stable</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Charts -->
        <div class="row mb-5">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Daily Revenue Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyRevenueChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-pie-chart"></i> Revenue by Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueByStatusChart" height="280"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods and Order Status -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-credit-card"></i> Payment Methods</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($paymentMethods as $method)
                            <div class="list-group-item px-0 py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ ucfirst($method->payment_method) }}</h6>
                                        <small class="text-muted">{{ $method->count }} transactions</small>
                                    </div>
                                    <div class="text-right">
                                        <strong>${{ number_format($method->revenue, 2) }}</strong>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar" style="width: {{ ($method->revenue / $totalRevenue) * 100 }}%;"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Order Status Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($revenueByStatus as $status)
                            <div class="list-group-item px-0 py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="badge bg-{{ $status['status'] === 'completed' ? 'success' : ($status['status'] === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($status['status']) }}
                                            </span>
                                        </h6>
                                        <small class="text-muted">{{ $status['count'] }} orders</small>
                                    </div>
                                    <div class="text-right">
                                        <strong>${{ number_format($status['revenue'], 2) }}</strong>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-{{ $status['status'] === 'completed' ? 'success' : 'warning' }}" style="width: {{ ($status['revenue'] / $totalRevenue) * 100 }}%;"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="row mb-5">
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-star"></i> Top Products</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                    <th>Avg Price</th>
                                    <th>% of Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                <tr>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td>{{ $product->orderItems()->count() }}</td>
                                    <td>${{ number_format($product->orderItems()->sum('total_price'), 2) }}</td>
                                    <td>${{ number_format($product->price, 2) }}</td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar" style="width: {{ ($product->orderItems()->sum('total_price') / $totalRevenue) * 100 }}%;"></div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Geographic Analysis -->
        <div class="row mb-5">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-globe"></i> Orders by Country</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Country</th>
                                    <th>Orders</th>
                                    <th>Revenue</th>
                                    <th>Avg Order Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ordersByCountry as $country)
                                <tr>
                                    <td>{{ $country->country }}</td>
                                    <td>{{ $country->count }}</td>
                                    <td>${{ number_format($country->revenue, 2) }}</td>
                                    <td>${{ number_format($country->revenue / $country->count, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-undo"></i> Refund Metrics</h5>
                    </div>
                    <div class="card-body">
                        <div class="metric-item mb-4">
                            <small class="text-muted">Total Refunds</small>
                            <h4 class="mt-2">{{ $refunds['totalRefunds'] }}</h4>
                        </div>
                        <div class="metric-item mb-4">
                            <small class="text-muted">Refund Amount</small>
                            <h4 class="mt-2">${{ number_format($refunds['refundAmount'], 2) }}</h4>
                        </div>
                        <div class="metric-item">
                            <small class="text-muted">Refund Rate</small>
                            <h4 class="mt-2">{{ $refunds['refundRate'] }}%</h4>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: {{ $refunds['refundRate'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.metrics-card {
    transition: transform 0.3s ease;
}

.metrics-card:hover {
    transform: translateY(-4px);
}

.metric-item {
    padding: 15px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 8px;
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Revenue Chart
    const ctx1 = document.getElementById('dailyRevenueChart').getContext('2d');
    const dailyData = @json($dailyRevenue);
    const labels1 = Object.keys(dailyData);
    const data1 = Object.values(dailyData);

    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: labels1,
            datasets: [{
                label: 'Daily Revenue',
                data: data1,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Revenue by Status Chart
    const ctx2 = document.getElementById('revenueByStatusChart').getContext('2d');
    const statusData = @json($revenueByStatus);
    
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: statusData.map(s => s.status),
            datasets: [{
                data: statusData.map(s => s.revenue),
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderColor: '#fff',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});

function exportReport(format) {
    const period = new URLSearchParams(window.location.search).get('period') || '30';
    window.location.href = `/admin/reports/export?report=sales&format=${format}&period=${period}`;
}
</script>
@endpush
@endsection
