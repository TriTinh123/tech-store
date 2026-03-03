@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">❌ Thanh Toán Không Thành Công</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-danger" role="alert">
                        <h5>Đơn hàng #{{ $order->id }} - Thanh toán bị hủy</h5>
                    </div>

                    <div class="mb-4">
                        <p class="text-muted mb-3">Thanh toán của bạn không được hoàn thành. Vui lòng thử lại hoặc chọn phương thức thanh toán khác.</p>

                        <div class="card mb-3">
                            <div class="card-body">
                                <p><strong>Mã Đơn Hàng:</strong> {{ $order->order_number }}</p>
                                <p><strong>Tổng Tiền:</strong> <span class="text-danger">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span></p>
                                <p><strong>Phương Thức Thanh Toán:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('payment.process', $order) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-redo"></i> Thử Lại Thanh Toán
                        </a>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-info-circle"></i> Xem Chi Tiết Đơn Hàng
                        </a>
                    </div>

                    <hr class="my-4">

                    <div class="alert alert-info">
                        <h6>💡 Có vấn đề?</h6>
                        <p class="mb-0">Nếu bạn gặp khó khăn, vui lòng <a href="mailto:support@shop.com">liên hệ chúng tôi</a> để được hỗ trợ.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
