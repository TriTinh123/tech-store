@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h1 style="color: #667eea; font-weight: bold;">
        <i class="fas fa-receipt"></i> Chi Tiết Đơn Hàng #{{ $order->id }}
    </h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="mb-0">Thông Tin Giao Hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tên:</strong> {{ $order->customer_name ?? 'Không có' }}</p>
                            <p><strong>Email:</strong> {{ $order->customer_email ?? 'Không có' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Điện thoại:</strong> {{ $order->customer_phone ?? 'Không có' }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $order->customer_address ?? 'Không có' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="mb-0">Sản Phẩm</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th>Sản Phẩm</th>
                                    <th class="text-center">Số Lượng</th>
                                    <th class="text-end">Giá</th>
                                    <th class="text-end">Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'Sản phẩm' }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">₫{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-end"><strong>₫{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="mb-0">Trạng Thái Đơn Hàng</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Cập Nhật Trạng Thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pending" @if($order->status == 'pending') selected @endif>Chờ xử lý</option>
                                <option value="confirmed" @if($order->status == 'confirmed') selected @endif>Xác nhận</option>
                                <option value="shipped" @if($order->status == 'shipped') selected @endif>Đang giao</option>
                                <option value="delivered" @if($order->status == 'delivered') selected @endif>Đã giao</option>
                                <option value="cancelled" @if($order->status == 'cancelled') selected @endif>Hủy</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check"></i> Cập Nhật
                        </button>
                    </form>

                    <hr>

                    <div class="info-box">
                        <p><strong>Ngày Đặt:</strong><br>{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Tổng Cộng:</strong><br><span style="color: #48bb78; font-size: 1.3em; font-weight: bold;">₫{{ number_format($order->total_amount, 0, ',', '.') }}</span></p>
                        <p><strong>Khách Hàng:</strong><br>{{ $order->user->name ?? 'Guest' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.orders') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay Lại Danh Sách
        </a>
    </div>
</div>

<style>
    .info-box {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }

    .info-box p {
        margin-bottom: 15px;
        color: #495057;
    }

    .info-box p:last-child {
        margin-bottom: 0;
    }
</style>
@endsection
