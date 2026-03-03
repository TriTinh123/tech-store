@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">💳 Thanh Toán Chuyển Khoản</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Mã Đơn Hàng: <strong>{{ $order->order_number }}</strong></h5>
                        <p class="mb-0">Vui lòng chuyển khoản theo thông tin bên dưới để hoàn tất thanh toán.</p>
                    </div>

                    <div class="row mt-4">
                        <!-- QR Code -->
                        <div class="col-md-6 text-center mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">📱 Quét Mã QR</h6>
                                </div>
                                <div class="card-body">
                                    @if($paymentData['qr_image'])
                                        <img src="{{ $paymentData['qr_image'] }}" alt="QR Code" class="img-fluid" style="max-width: 280px;">
                                        <p class="text-muted small mt-3">Quét mã QR này bằng ứng dụng ngân hàng của bạn</p>
                                    @else
                                        <div class="alert alert-warning">
                                            Không thể tạo mã QR. Vui lòng sử dụng thông tin chuyển khoản dưới đây.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Bank Info -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">🏦 Thông Tin Chuyển Khoản</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label text-muted"><strong>Ngân Hàng</strong></label>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="fs-5 mb-0"><strong>{{ $paymentData['bank_info']['bank_name'] ?? 'Vietcombank' }}</strong></p>
                                            <button class="btn btn-sm btn-outline-primary" type="button" onclick="copyToClipboard('{{ $paymentData['bank_info']['bank_name'] ?? 'Vietcombank' }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-muted"><strong>Số Tài Khoản</strong></label>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="fs-5 mb-0"><strong class="text-danger">{{ $paymentData['bank_info']['account_number'] }}</strong></p>
                                            <button class="btn btn-sm btn-outline-primary" type="button" onclick="copyToClipboard('{{ $paymentData['bank_info']['account_number'] }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-muted"><strong>Chủ Tài Khoản</strong></label>
                                        <p class="fs-5 mb-0"><strong>{{ $paymentData['bank_info']['account_holder'] ?? 'N/A' }}</strong></p>
                                    </div>

                                    <hr>

                                    <div class="mb-3">
                                        <label class="form-label text-muted"><strong>Số Tiền Cần Chuyển</strong></label>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="fs-4 mb-0"><strong class="text-danger">{{ number_format($paymentData['amount'], 0, ',', '.') }}₫</strong></p>
                                            <button class="btn btn-sm btn-outline-primary" type="button" onclick="copyToClipboard('{{ $paymentData['amount'] }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-muted"><strong>Nội Dung Chuyển Khoản</strong></label>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="fs-6 mb-0"><strong>{{ $paymentData['order_number'] }}</strong></p>
                                            <button class="btn btn-sm btn-outline-primary" type="button" onclick="copyToClipboard('{{ $paymentData['order_number'] }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Form -->
                    <div class="card mt-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">✓ Xác Minh Chuyển Khoản</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Sau khi chuyển khoản thành công, bấm nút dưới để xác minh:</p>

                            <form method="POST" action="{{ route('payment.confirm-bank', $order) }}" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 btn-lg">
                                    <i class="fas fa-check-circle"></i> Tôi Đã Chuyển Khoản - Xác Minh Thanh Toán
                                </button>
                            </form>

                            <p class="text-muted small">
                                💡 <strong>Lưu ý:</strong> Bấm nút trên sau khi bạn đã chuyển khoản thành công. Đơn hàng sẽ được xử lý ngay lập tức.
                            </p>

                            <hr>

                            <p class="text-muted small mb-2"><strong>Cần giúp?</strong></p>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Quay Lại
                            </a>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Đã sao chép: ' + text);
    });
}
</script>
@endsection
