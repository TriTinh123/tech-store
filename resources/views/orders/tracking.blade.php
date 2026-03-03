@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h4 class="mb-0 text-white">📍 Theo Dõi Đơn Hàng</h4>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <p><strong>Mã Đơn Hàng:</strong> <span class="text-primary">{{ $order->order_number }}</span></p>
                        <p><strong>Trạng Thái Thanh Toán:</strong> 
                            <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </p>
                        <p><strong>Trạng Thái Vận Chuyển:</strong> 
                            <span class="badge bg-info">{{ ucfirst($order->shipping_status ?? 'pending') }}</span>
                        </p>
                    </div>

                    @if($order->tracking_number && $order->shipping_provider)
                    <div class="tracking-info">
                        <h6 class="mb-3">📦 Thông Tin Vận Chuyển</h6>
                        
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="text-muted mb-1">Nhà Vận Chuyển</p>
                                        <p><strong>{{ $order->shipping_provider }}</strong></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted mb-1">Mã Theo Dõi</p>
                                        <p><strong>{{ $order->tracking_number }}</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100 mb-3" id="trackBtn" onclick="fetchTracking()">
                            <i class="fas fa-sync"></i> Cập Nhật Theo Dõi
                        </button>

                        <div id="trackingResult" class="d-none">
                            <div class="alert alert-info">
                                <strong id="trackingStatus"></strong>
                            </div>

                            <div id="trackingTimeline" class="timeline"></div>
                        </div>

                        <div id="trackingError" class="d-none">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> <span id="errorMessage"></span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> Chưa có thông tin vận chuyển. Admin sẽ cập nhật sớm nhất.
                    </div>
                    @endif

                    <!-- Delivery Address -->
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">📍 Địa Chỉ Giao Hàng</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ $order->customer_name }}</strong></p>
                            <p>{{ $order->delivery_address }}</p>
                            <p>Điện thoại: {{ $order->customer_phone }}</p>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">📦 Sản Phẩm Đặt Hàng</h6>
                        </div>
                        <div class="card-body">
                            @foreach($order->items as $item)
                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-md-2">
                                    <img src="{{ $item->product->image ?? 'https://via.placeholder.com/100' }}" 
                                         class="img-fluid rounded" alt="{{ $item->product_name }}">
                                </div>
                                <div class="col-md-6">
                                    <p><strong>{{ $item->product_name }}</strong></p>
                                    <p class="text-muted">Số lượng: {{ $item->quantity }}</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <p>{{ number_format($item->subtotal, 0, ',', '.') }}₫</p>
                                </div>
                            </div>
                            @endforeach

                            <div class="border-top pt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tổng Cộng:</span>
                                    <strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Quay Lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card shadow mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">📋 Tóm Tắt Đơn Hàng</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phương Thức Thanh Toán:</span>
                        <strong>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Trạng Thái:</span>
                        <span class="badge bg-primary">{{ ucfirst($order->status) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Ngày Đặt:</span>
                        <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Return Request -->
            @if($order->returns->isNotEmpty())
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="mb-0">↩️ Yêu Cầu Hoàn Trả</h6>
                </div>
                <div class="card-body">
                    @foreach($order->returns as $return)
                    <p><strong>Mã:</strong> #{{ $return->id }}</p>
                    <p><strong>Trạng Thái:</strong> 
                        <span class="badge bg-{{ $return->status === 'approved' ? 'success' : ($return->status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($return->status) }}
                        </span>
                    </p>
                    <p><strong>Số Tiền:</strong> {{ number_format($return->refund_amount, 0, ',', '.') }}₫</p>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    padding-left: 40px;
    padding-bottom: 20px;
    position: relative;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: #667eea;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #667eea;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 9px;
    top: 20px;
    width: 2px;
    height: 60px;
    background-color: #ddd;
}
</style>

<script>
function fetchTracking() {
    const btn = document.getElementById('trackBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';

    fetch('{{ route("api.tracking.track", $order) }}', {
        headers: {
            'Authorization': 'Bearer ' + document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync"></i> Cập Nhật Theo Dõi';

        if (data.success) {
            displayTracking(data.data);
        } else {
            showError(data.error || 'Không thể lấy thông tin vận chuyển');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync"></i> Cập Nhật Theo Dõi';
        showError('Lỗi: ' + error.message);
    });
}

function displayTracking(data) {
    document.getElementById('trackingError').classList.add('d-none');
    document.getElementById('trackingResult').classList.remove('d-none');
    document.getElementById('trackingStatus').textContent = data.status;

    const timeline = document.getElementById('trackingTimeline');
    timeline.innerHTML = '';

    if (data.timeline && data.timeline.length > 0) {
        data.timeline.forEach(event => {
            const item = document.createElement('div');
            item.className = 'timeline-item';
            item.innerHTML = `
                <strong>${event.status || 'Cập nhật'}</strong>
                <p class="text-muted mb-0">${event.time || new Date().toLocaleString('vi-VN')}</p>
            `;
            timeline.appendChild(item);
        });
    } else {
        timeline.innerHTML = '<p class="text-muted">Chưa có cập nhật theo dõi</p>';
    }
}

function showError(message) {
    document.getElementById('trackingResult').classList.add('d-none');
    document.getElementById('trackingError').classList.remove('d-none');
    document.getElementById('errorMessage').textContent = message;
}

// Auto-fetch tracking on page load
document.addEventListener('DOMContentLoaded', function() {
    @if($order->tracking_number && $order->shipping_provider)
        fetchTracking();
    @endif
});
</script>
@endsection
