@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-lg-12">
            <h2>💳 Quản Lý Thanh Toán</h2>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Tổng Thanh Toán Hôm Nay</h6>
                    <h3 class="text-success">{{ number_format($todayPayment, 0, ',', '.') }}₫</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Chưa Thanh Toán</h6>
                    <h3 class="text-danger">{{ number_format($pendingPayment, 0, ',', '.') }}₫</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Đã Thanh Toán</h6>
                    <h3 class="text-success">{{ number_format($paidPayment, 0, ',', '.') }}₫</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Tỷ Lệ Thành Công</h6>
                    <h3>{{ $successRate }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods Chart -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Phương Thức Thanh Toán</h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Trạng Thái Thanh Toán</h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Payments Table -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">⏳ Thanh Toán Chờ Xác Nhận</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Số Tiền</th>
                        <th>Phương Thức</th>
                        <th>Trạng Thái</th>
                        <th>Ngày</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPayments as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ $order->customer_name }}</td>
                        <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong></td>
                        <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span></td>
                        <td><span class="badge bg-warning">{{ ucfirst($order->payment_status) }}</span></td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($order->payment_method === 'bank_transfer')
                            <button class="btn btn-sm btn-success" onclick="confirmPayment({{ $order->id }})">
                                <i class="fas fa-check"></i> Xác Minh
                            </button>
                            @endif
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Không có thanh toán chờ xác nhận</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-header bg-light">
            <h6 class="mb-0">📊 Giao Dịch Gần Đây</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Số Tiền</th>
                        <th>Phương Thức</th>
                        <th>Trạng Thái</th>
                        <th>Thanh Toán</th>
                        <th>Ngày</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                        <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span></td>
                        <td>
                            @switch($order->status)
                                @case('processing')
                                    <span class="badge bg-info">Đang xử lý</span>
                                    @break
                                @case('shipped')
                                    <span class="badge bg-primary">Đã gửi</span>
                                    @break
                                @case('delivered')
                                    <span class="badge bg-success">Đã giao</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-danger">Hủy</span>
                                    @break
                                @default
                                    <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                            @endswitch
                        </td>
                        <td>
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success">✓ Đã Thanh Toán</span>
                            @else
                                <span class="badge bg-warning">Chờ</span>
                            @endif
                        </td>
                        <td>{{ $order->paid_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Không có giao dịch</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Payment Method Chart
const methodCtx = document.getElementById('paymentMethodChart').getContext('2d');
new Chart(methodCtx, {
    type: 'doughnut',
    data: {
        labels: ['Stripe', 'PayPal', 'Chuyển Khoản', 'COD'],
        datasets: [{
            data: [
                {{ $paymentMethods['stripe'] ?? 0 }},
                {{ $paymentMethods['paypal'] ?? 0 }},
                {{ $paymentMethods['bank_transfer'] ?? 0 }},
                {{ $paymentMethods['cod'] ?? 0 }}
            ],
            backgroundColor: ['#667eea', '#003087', '#0066cc', '#ff9500']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Payment Status Chart
const statusCtx = document.getElementById('paymentStatusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: ['Đã Thanh Toán', 'Chưa Thanh Toán', 'Hủy'],
        datasets: [{
            data: [
                {{ $paymentStatus['paid'] ?? 0 }},
                {{ $paymentStatus['pending'] ?? 0 }},
                {{ $paymentStatus['cancelled'] ?? 0 }}
            ],
            backgroundColor: ['#28a745', '#ffc107', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

function confirmPayment(orderId) {
    if (!confirm('Xác nhận thanh toán cho đơn hàng #' + orderId + '?')) return;
    
    fetch('/admin/payments/' + orderId + '/confirm', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Đã xác nhận thanh toán');
            location.reload();
        } else {
            alert('Lỗi: ' + data.message);
        }
    });
}
</script>
@endsection
