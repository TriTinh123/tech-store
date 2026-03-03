@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <h4 class="mb-0 text-white">🎉 Đơn Hàng Đã Được Tạo Thành Công!</h4>
                </div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <div style="font-size: 60px; color: #28a745;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>

                    <div class="alert alert-success">
                        <h5 class="mb-2">Cảm ơn bạn đã đặt hàng! 🙏</h5>
                        <p class="mb-0">Chúng tôi đã nhận được đơn hàng của bạn và sẽ chuẩn bị hàng để gửi đi sớm nhất.</p>
                    </div>

                    <!-- Order Info -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">📋 Thông Tin Đơn Hàng</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="text-muted mb-1"><strong>Mã Đơn Hàng</strong></p>
                                    <p class="fs-5 text-primary"><strong>{{ $order->order_number }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1"><strong>Ngày Đặt</strong></p>
                                    <p class="fs-5">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-muted mb-1"><strong>Tổng Tiền</strong></p>
                                    <p class="fs-5 text-danger"><strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1"><strong>Trạng Thái</strong></p>
                                    <span class="badge bg-primary fs-6">{{ ucfirst($order->status) }}</span>
                                </div>
                            </div>

                            <hr>

                            <p class="text-muted mb-1"><strong>Phương Thức Thanh Toán</strong></p>
                            <p class="fs-6">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>

                            <p class="text-muted mb-1"><strong>Trạng Thái Thanh Toán</strong></p>
                            <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }} fs-6">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">📦 Sản Phẩm Đặt Hàng ({{ $order->items->count() }} sản phẩm)</h6>
                        </div>
                        <div class="card-body">
                            @foreach($order->items as $item)
                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-md-2">
                                    <img src="{{ $item->product->image ?? 'https://via.placeholder.com/80' }}" 
                                         class="img-fluid rounded" alt="{{ $item->product_name }}">
                                </div>
                                <div class="col-md-6">
                                    <p><strong>{{ $item->product_name }}</strong></p>
                                    <p class="text-muted mb-0">Số lượng: {{ $item->quantity }}</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <p><strong>{{ number_format($item->subtotal, 0, ',', '.') }}₫</strong></p>
                                </div>
                            </div>
                            @endforeach

                            <div class="border-top pt-3">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-6"><strong>Tổng Cộng:</strong></span>
                                    <strong class="fs-5 text-danger">{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">📍 Địa Chỉ Giao Hàng</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ $order->customer_name }}</strong></p>
                            <p>{{ $order->delivery_address }}</p>
                            <p class="mb-0">📞 {{ $order->customer_phone }}</p>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="alert alert-info">
                        <h6 class="mb-2">📧 Tiếp Theo</h6>
                        <ul class="mb-0">
                            <li>Email xác nhận đã được gửi đến: <strong>{{ $order->customer_email }}</strong></li>
                            <li>Bạn có thể theo dõi đơn hàng tại trang <strong>Đơn Hàng Của Tôi</strong></li>
                            <li>Chúng tôi sẽ gửi email thông báo khi hàng được gửi đi</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-box"></i> Xem Chi Tiết Đơn Hàng
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> Xem Tất Cả Đơn Hàng
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home"></i> Tiếp Tục Mua Sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}
</style>
@endsection
