@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-receipt"></i> Chi Tiết Đơn Hàng #{{ $order->id }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <h5 style="color: #11998e; font-weight: bold;">
                                    <i class="fas fa-calendar"></i> Ngày Đặt
                                </h5>
                                <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <h5 style="color: #11998e; font-weight: bold;">
                                    <i class="fas fa-info-circle"></i> Trạng Thái
                                </h5>
                                <p>
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock"></i> Đang xử lý
                                        </span>
                                    @elseif($order->status == 'confirmed')
                                        <span class="badge bg-info">
                                            <i class="fas fa-check"></i> Đã xác nhận
                                        </span>
                                    @elseif($order->status == 'shipped')
                                        <span class="badge bg-primary">
                                            <i class="fas fa-truck"></i> Đang giao
                                        </span>
                                    @elseif($order->status == 'delivered')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> Đã giao
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle"></i> Hủy
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h4 style="color: #11998e; font-weight: bold; margin: 25px 0;">
                        <i class="fas fa-box"></i> Sản Phẩm Trong Đơn Hàng
                    </h4>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead style="background-color: #f8f9fa; border-top: 3px solid #11998e;">
                                <tr>
                                    <th style="color: #11998e; font-weight: bold;">Sản Phẩm</th>
                                    <th style="color: #11998e; font-weight: bold;" class="text-center">Số Lượng</th>
                                    <th style="color: #11998e; font-weight: bold;" class="text-end">Giá</th>
                                    <th style="color: #11998e; font-weight: bold;" class="text-end">Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product->name ?? 'Sản phẩm' }}</strong>
                                            <br>
                                            <small class="text-muted">SKU: {{ $item->product->id ?? 'N/A' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end">
                                            ₫{{ number_format($item->price, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            <strong style="color: #38ef7d; font-size: 1.05em;">
                                                ₫{{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                            </strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <h4 style="color: #11998e; font-weight: bold; margin: 25px 0;">
                        <i class="fas fa-user"></i> Thông Tin Giao Hàng
                    </h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <p><strong>Tên:</strong> {{ $order->customer_name ?? 'Không có' }}</p>
                                <p><strong>Email:</strong> {{ $order->customer_email ?? 'Không có' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <p><strong>Điện thoại:</strong> {{ $order->customer_phone ?? 'Không có' }}</p>
                                <p><strong>Địa chỉ:</strong> {{ $order->customer_address ?? 'Không có' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6">
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                                <h5 style="color: #11998e; font-weight: bold; margin-bottom: 15px;">
                                    <i class="fas fa-calculator"></i> Tóm Tắt
                                </h5>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tổng sản phẩm:</span>
                                    <strong>₫{{ number_format($order->total_amount - 0, 0, ',', '.') }}</strong>
                                </div>

                                <div class="d-flex justify-content-between mb-2" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                                    <span>Phí vận chuyển:</span>
                                    <strong>Miễn phí</strong>
                                </div>

                                <div class="d-flex justify-content-between" style="font-size: 1.2em;">
                                    <span style="color: #11998e; font-weight: bold;">Tổng cộng:</span>
                                    <strong style="color: #38ef7d; font-size: 1.3em;">
                                        ₫{{ number_format($order->total_amount, 0, ',', '.') }}
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Quay Lại Danh Sách
                        </a>
                        <a href="{{ route('profile.show') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-user"></i> Hồ Sơ Cá Nhân
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-box {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .info-box h5 {
        margin-bottom: 10px;
    }

    .info-box p {
        margin-bottom: 5px;
        color: #495057;
    }

    .table {
        font-size: 0.95rem;
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.5rem 0.8rem;
        border-radius: 6px;
    }

    .btn-lg {
        transition: all 0.3s ease;
    }

    .btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header {
        border-bottom: 3px solid #11998e;
    }
</style>
@endsection
