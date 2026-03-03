@extends('layouts.app')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, {{ $paymentDetails['color'] ?? '#667eea' }} 0%, {{ $paymentDetails['color'] ?? '#764ba2' }} 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="{{ $paymentDetails['icon'] }}"></i> {{ $paymentDetails['title'] }}
                    </h3>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    <!-- Payment Details -->
                    <div class="alert alert-light" style="border: 2px solid {{ $paymentDetails['color'] }};">
                        <h5 style="color: {{ $paymentDetails['color'] }};"><i class="fas fa-info-circle"></i> {{ $paymentDetails['description'] }}</h5>
                    </div>

                    @if($gateway === 'cod')
                        <!-- COD Payment Details -->
                        <div class="payment-instructions">
                            <h5 style="color: {{ $paymentDetails['color'] }};">Hướng dẫn thanh toán</h5>
                            <ol class="mt-3">
                                @foreach($paymentDetails['steps'] as $step)
                                    <li class="mb-2">{{ $step }}</li>
                                @endforeach
                            </ol>

                            <div class="alert alert-warning mt-4">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Lưu ý:</strong> {{ $paymentDetails['note'] }}
                            </div>

                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                                <h6 style="color: {{ $paymentDetails['color'] }};">Số tiền phải thanh toán:</h6>
                                <h3 style="color: {{ $paymentDetails['color'] }}; font-weight: bold;">{{ number_format($paymentDetails['amount'], 0, ',', '.') }}₫</h3>
                            </div>
                        </div>
                    @elseif($gateway === 'bank_transfer')
                        <!-- Bank Transfer Details -->
                        <div class="payment-instructions">
                            <h5 style="color: {{ $paymentDetails['color'] }};">Thông Tin Chuyển Khoản</h5>
                            
                            <!-- Bank Accounts -->
                            <div class="row mt-3">
                                @foreach($paymentDetails['banks'] as $bank)
                                    <div class="col-md-6 mb-3">
                                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid {{ $paymentDetails['color'] }};">
                                            <p><strong>{{ $bank['bank'] }}</strong></p>
                                            <p class="mb-1"><small>Số tài khoản:</small><br><code>{{ $bank['account'] }}</code></p>
                                            <p class="mb-1"><small>Chủ tài khoản:</small><br><strong>{{ $bank['holder'] }}</strong></p>
                                            <p class="mb-0"><small>Chi nhánh:</small><br>{{ $bank['branch'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Reference Number -->
                            <div style="background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;">
                                <h6><i class="fas fa-key"></i> Mã Tham Chiếu (Ghi vào nội dung chuyển khoản):</h6>
                                <p style="font-size: 18px; font-weight: bold; color: #ff6b6b; margin-top: 10px; word-break: break-all;">{{ $paymentDetails['reference'] }}</p>
                            </div>

                            <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2196F3;">
                                <h6>Số tiền phải thanh toán:</h6>
                                <h3 style="color: {{ $paymentDetails['color'] }}; font-weight: bold;">{{ number_format($paymentDetails['amount'], 0, ',', '.') }}₫</h3>
                            </div>

                            <!-- Instructions -->
                            <h6 style="color: {{ $paymentDetails['color'] }}; margin-top: 30px;">Các Bước Thực Hiện:</h6>
                            <ol class="mt-2">
                                @foreach($paymentDetails['steps'] as $step)
                                    <li class="mb-2">{{ $step }}</li>
                                @endforeach
                            </ol>

                            <div class="alert alert-info mt-4">
                                <i class="fas fa-lightbulb"></i> 
                                <strong>Mẹo:</strong> {{ $paymentDetails['note'] }}
                            </div>
                        </div>
                    @endif

                    <!-- QR Code Section for Momo and Zalo Pay -->
                    @if($gateway === 'momo' || $gateway === 'zalopay')
                        <div style="background: #f8f9fa; padding: 30px; border-radius: 8px; margin: 20px 0; text-align: center;">
                            <h5 style="color: {{ $paymentDetails['color'] }};">Quét mã QR để thanh toán</h5>
                            <p class="text-muted">Sử dụng ứng dụng {{ $gateway === 'momo' ? 'Momo' : 'Zalo' }} để quét mã dưới đây</p>
                            
                            @php
                                // Generate QR code data
                                $paymentData = '';
                                if($gateway === 'momo') {
                                    $paymentData = "https://nhantien.momo.vn/" . ($paymentDetails['qr_url'] ?? 'payment');
                                } else if($gateway === 'zalopay') {
                                    $paymentData = $paymentDetails['qr_url'] ?? 'zalopay://payment';
                                }
                                
                                if(!$paymentData) {
                                    $paymentData = "Payment Reference: " . ($paymentDetails['reference'] ?? $order->order_number);
                                }
                                
                                // Encode for QR code URL - using qr-server API
                                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($paymentData);
                            @endphp
                            
                            <img src="{{ $qrUrl }}" alt="QR Code" style="width: 250px; height: 250px; margin: 20px 0; border: 2px solid {{ $paymentDetails['color'] }}; padding: 10px; background: white; border-radius: 8px;">
                            
                            <p class="mt-3 text-muted">
                                <small>Không thể quét? <a href="{{ $paymentDetails['payment_url'] ?? '#' }}">Nhấp vào đây để thanh toán</a></small>
                            </p>
                        </div>
                        
                        <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2196F3;">
                            <h6>Số tiền phải thanh toán:</h6>
                            <h3 style="color: {{ $paymentDetails['color'] }}; font-weight: bold;">{{ number_format($paymentDetails['amount'], 0, ',', '.') }}₫</h3>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Lưu ý:</strong> {{ $paymentDetails['note'] ?? 'Vui lòng hoàn thành thanh toán trong vòng 15 phút' }}
                        </div>
                    @endif

                    <!-- QR Code for Bank Transfer -->
                    @if($gateway === 'bank_transfer')
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;" class="mt-4">
                            <h5 style="color: {{ $paymentDetails['color'] }};">QR Code Chuyển Khoản</h5>
                            <p class="text-muted">Quét QR code với ứng dụng banking để chuyển khoản nhanh chóng</p>
                            
                            @php
                                // Create VietQR string format: https://vietqr.io/{bank_code}/{account_number}/{amount}/{description}
                                $bankCode = '970422'; // Vietcombank code (you can make this dynamic)
                                $account = '1234567890'; // Default - adjust based on selected bank
                                $amount = (int)$paymentDetails['amount'];
                                $reference = $paymentDetails['reference'] ?? $order->order_number;
                                
                                $vietqrData = "https://vietqr.io/{$bankCode}/{$account}/{$amount}/{$reference}";
                                
                                // Alternative: Use QRServer API
                                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=' . urlencode($vietqrData);
                            @endphp
                            
                            <img src="{{ $qrUrl }}" alt="QR Code Chuyển Khoản" style="width: 280px; height: 280px; margin: 20px 0; border: 3px solid {{ $paymentDetails['color'] }}; padding: 10px; background: white; border-radius: 8px;">
                            
                            <p class="mt-3 text-muted"><small>Quét bằng ứng dụng ngân hàng hoặc Momo, Zalo Pay</small></p>
                        </div>
                    @endif

                    <hr class="my-4">

                    <h5>Thông Tin Đơn Hàng</h5>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Mã đơn hàng:</strong></td>
                            <td>{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Khách hàng:</strong></td>
                            <td>{{ $order->customer_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $order->customer_email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Điện thoại:</strong></td>
                            <td>{{ $order->customer_phone }}</td>
                        </tr>
                        <tr>
                            <td><strong>Địa chỉ giao hàng:</strong></td>
                            <td>{{ $order->delivery_address }}</td>
                        </tr>
                        <tr style="background: #f8f9fa;">
                            <td><strong>Tổng tiền:</strong></td>
                            <td style="color: #c2185b; font-weight: bold; font-size: 18px;">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                        </tr>
                    </table>

                    <!-- Action Buttons -->
                    <div class="mt-4">
                        @if($gateway === 'bank_transfer')
                            <!-- Bank Transfer Confirmation -->
                            <form action="{{ route('payment.confirm-transfer', $order) }}" method="POST" style="display: inline-block; margin-right: 10px;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check-circle"></i> Tôi Đã Chuyển Khoản
                                </button>
                            </form>
                            <a href="{{ route('payment.method', $order) }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Quay Lại
                            </a>
                        @else
                            <!-- Other Payment Methods -->
                            <a href="{{ route('payment.method', $order) }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Quay Lại
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-instructions ol {
        padding-left: 20px;
    }

    .payment-instructions li {
        line-height: 1.8;
    }

    code {
        background: #f5f5f5;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 14px;
    }
</style>
@endsection
