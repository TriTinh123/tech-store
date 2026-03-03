@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">✓ Thanh Toán Thành Công</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-success" role="alert">
                        <h5>Cảm ơn bạn đã đặt hàng!</h5>
                    </div>

                    <div class="mb-4">
                        <p class="text-muted mb-3">Đơn hàng của bạn đã được xác nhận. Chúng tôi sẽ chuẩn bị hàng và gửi cho bạn sớm nhất.</p>

                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Thông Tin Đơn Hàng</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Mã Đơn Hàng:</strong></p>
                                        <p class="text-primary fs-5">{{ $order->order_number }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Ngày Đặt:</strong></p>
                                        <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>

                                <hr>

                                <p><strong>Tổng Tiền:</strong> <span class="text-success fs-5">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span></p>
                                <p><strong>Phương Thức Thanh Toán:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                                <p><strong>Trạng Thái Thanh Toán:</strong> 
                                    <span class="badge bg-success">{{ ucfirst($order->payment_status) }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Chi Tiết Sản Phẩm ({{ $order->items->count() }} sản phẩm)</h6>
                            </div>
                            <div class="card-body">
                                @foreach($order->items as $item)
                                <div class="row mb-3 pb-3 border-bottom">
                                    <div class="col-md-8">
                                        <p><strong>{{ $item->product_name }}</strong></p>
                                        <p class="text-muted">Số lượng: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <p>{{ number_format($item->subtotal, 0, ',', '.') }}₫</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-box"></i> Xem Trạng Thái Đơn Hàng
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-home"></i> Tiếp Tục Mua Sắm
                        </a>
                    </div>

                    <hr class="my-4">

                    <div class="alert alert-info">
                        <h6>📧 Xác Nhận Đơn Hàng</h6>
                        <p class="mb-0">Bạn sẽ nhận được email xác nhận tại <strong>{{ $order->customer_email }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
