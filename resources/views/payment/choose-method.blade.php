@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h4 class="mb-0 text-white">💳 Chọn Phương Thức Thanh Toán</h4>
                </div>

                <div class="card-body">
                    <div class="order-summary mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3">Tóm Tắt Đơn Hàng</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng Tiền:</span>
                            <strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong>
                        </div>
                        @if($order->discount_amount)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Giảm Giá:</span>
                            <strong>-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</strong>
                        </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span><strong>Cần Thanh Toán:</strong></span>
                            <strong class="text-danger fs-5">{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong>
                        </div>
                    </div>

                    <h6 class="mb-3">Chọn Phương Thức Thanh Toán</h6>

                    <div class="row g-3">
                        <!-- Stripe -->
                        <div class="col-md-6">
                            <div class="card payment-option cursor-pointer" onclick="selectPayment('stripe')">
                                <div class="card-body text-center">
                                    <div class="payment-icon mb-3">
                                        <i class="fas fa-credit-card fa-3x text-primary"></i>
                                    </div>
                                    <h6>Stripe - Thẻ Tín Dụng</h6>
                                    <p class="text-muted small mb-0">Thanh toán bằng thẻ Visa, Mastercard</p>
                                    <small class="text-success">✓ An toàn và nhanh chóng</small>
                                </div>
                            </div>
                        </div>

                        <!-- PayPal -->
                        <div class="col-md-6">
                            <div class="card payment-option cursor-pointer" onclick="selectPayment('paypal')">
                                <div class="card-body text-center">
                                    <div class="payment-icon mb-3">
                                        <i class="fab fa-paypal fa-3x" style="color: #003087;"></i>
                                    </div>
                                    <h6>PayPal</h6>
                                    <p class="text-muted small mb-0">Thanh toán qua tài khoản PayPal</p>
                                    <small class="text-success">✓ Bảo vệ người mua</small>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer -->
                        <div class="col-md-6">
                            <div class="card payment-option cursor-pointer" onclick="selectPayment('bank_transfer')">
                                <div class="card-body text-center">
                                    <div class="payment-icon mb-3">
                                        <i class="fas fa-university fa-3x text-info"></i>
                                    </div>
                                    <h6>Chuyển Khoản Ngân Hàng</h6>
                                    <p class="text-muted small mb-0">Có mã QR để quét</p>
                                    <small class="text-success">✓ Hỗ trợ VietQR</small>
                                </div>
                            </div>
                        </div>

                        <!-- COD -->
                        <div class="col-md-6">
                            <div class="card payment-option cursor-pointer" onclick="selectPayment('cod')">
                                <div class="card-body text-center">
                                    <div class="payment-icon mb-3">
                                        <i class="fas fa-truck fa-3x text-warning"></i>
                                    </div>
                                    <h6>Thanh Toán Khi Nhận Hàng</h6>
                                    <p class="text-muted small mb-0">Không cần thanh toán trước</p>
                                    <small class="text-success">✓ Linh hoạt</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="paymentForm" method="POST" action="{{ route('payment.process', $order) }}" class="mt-4">
                        @csrf
                        <input type="hidden" id="paymentMethodInput" name="payment_method" value="">
                        
                        <button type="submit" id="confirmBtn" class="btn btn-primary btn-lg w-100" disabled>
                            <i class="fas fa-lock"></i> Tiến Hành Thanh Toán
                        </button>
                        <a href="{{ route('checkout.show') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left"></i> Quay Lại
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-option {
    border: 2px solid #f0f0f0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-option:hover {
    border-color: #667eea;
    box-shadow: 0 0 10px rgba(102, 126, 234, 0.1);
    transform: translateY(-5px);
}

.payment-option.selected {
    border-color: #667eea;
    background-color: #f8f9ff;
}

.payment-icon {
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script>
let selectedPayment = null;

function selectPayment(method) {
    selectedPayment = method;
    console.log('Selected payment method:', method);
    
    // Update UI - highlight selected option
    document.querySelectorAll('.payment-option').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Find the clicked card and add selected class
    event.currentTarget.closest('.payment-option').classList.add('selected');
    
    // Enable button
    document.getElementById('confirmBtn').disabled = false;
    console.log('Button enabled');
}

// Set form submit handler
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    
    form.addEventListener('submit', function(e) {
        console.log('Form submitting, selectedPayment:', selectedPayment);
        
        if (!selectedPayment) {
            e.preventDefault();
            alert('Vui lòng chọn phương thức thanh toán');
            return false;
        }
        
        // Set the payment method in hidden input
        document.getElementById('paymentMethodInput').value = selectedPayment;
        console.log('Setting payment method to:', selectedPayment);
        
        // Form will submit normally
        return true;
    });
});
</script>
@endsection
