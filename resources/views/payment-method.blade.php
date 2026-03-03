@extends('layouts.app')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-credit-card"></i> Chọn Phương Thức Thanh Toán
                    </h3>
                    <small>Đơn hàng #{{ $order->order_number }} - {{ number_format($order->total_amount, 0, ',', '.') }}₫</small>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row g-4">
                        <!-- COD Payment Method -->
                        <div class="col-md-6">
                            <form action="{{ route('payment.process', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_gateway" value="cod">
                                <button type="submit" class="btn payment-option w-100" style="border: 2px solid #00a699; background: #f8f9fa;">
                                    <div class="payment-icon" style="color: #00a699;">
                                        <i class="fas fa-hand-holding-usd fa-3x mb-2"></i>
                                    </div>
                                    <h5 style="color: #00a699;">Thanh Toán Khi Nhận Hàng</h5>
                                    <p class="text-muted small">Không cần thanh toán trước<br>Thanh toán khi nhân viên giao hàng</p>
                                </button>
                            </form>
                        </div>

                        <!-- Bank Transfer Payment Method -->
                        <div class="col-md-6">
                            <form action="{{ route('payment.process', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_gateway" value="bank_transfer">
                                <button type="submit" class="btn payment-option w-100" style="border: 2px solid #3498db; background: #f8f9fa;">
                                    <div class="payment-icon" style="color: #3498db;">
                                        <i class="fas fa-university fa-3x mb-2"></i>
                                    </div>
                                    <h5 style="color: #3498db;">Chuyển Khoản Ngân Hàng</h5>
                                    <p class="text-muted small">Chuyển khoản trực tiếp<br>Từ tài khoản ngân hàng của bạn</p>
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Order Summary -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Thông Tin Đơn Hàng</h6>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Tên khách hàng:</strong> {{ $order->customer_name }}</p>
                                <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                                <p><strong>Điện thoại:</strong> {{ $order->customer_phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Địa chỉ giao hàng:</strong> {{ $order->delivery_address }}</p>
                                <p><strong>Tổng tiền:</strong> <span style="color: #c2185b; font-weight: bold;">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('checkout.show') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay Lại
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home"></i> Trang Chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-option {
        padding: 20px;
        border-radius: 8px;
        transition: transform 0.3s, box-shadow 0.3s;
        text-decoration: none !important;
        color: inherit;
        cursor: pointer;
    }

    .payment-option:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        text-decoration: none !important;
    }

    .payment-option h5 {
        font-weight: 600;
        margin-bottom: 8px;
    }

    .payment-icon {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
@endsection
