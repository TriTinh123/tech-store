@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-truck"></i> Cập Nhật Vận Chuyển
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <strong>Đơn Hàng #{{ $order->id }}</strong> - {{ $order->customer_name }}<br>
                        <small>{{ $order->delivery_address }}</small>
                    </div>

                    <form action="{{ route('admin.shipping.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="tracking_number" class="form-label">Mã Tracking *</label>
                            <input type="text" id="tracking_number" name="tracking_number" class="form-control @error('tracking_number') is-invalid @enderror" 
                                   placeholder="VD: 1234567890" value="{{ old('tracking_number', $order->tracking_number) }}" required>
                            @error('tracking_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Mã theo dõi từ nhà vận chuyển</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="shipping_provider" class="form-label">Nhà Vận Chuyển *</label>
                            <select id="shipping_provider" name="shipping_provider" class="form-control @error('shipping_provider') is-invalid @enderror" required>
                                <option value="">-- Chọn nhà vận chuyển --</option>
                                <option value="GHN" {{ old('shipping_provider', $order->shipping_provider) === 'GHN' ? 'selected' : '' }}>GHN</option>
                                <option value="GRAB" {{ old('shipping_provider', $order->shipping_provider) === 'GRAB' ? 'selected' : '' }}>GRAB</option>
                                <option value="VIETTELPOST" {{ old('shipping_provider', $order->shipping_provider) === 'VIETTELPOST' ? 'selected' : '' }}>Viettel Post</option>
                                <option value="JNTEXPRESS" {{ old('shipping_provider', $order->shipping_provider) === 'JNTEXPRESS' ? 'selected' : '' }}>J&T Express</option>
                                <option value="AHAMOVE" {{ old('shipping_provider', $order->shipping_provider) === 'AHAMOVE' ? 'selected' : '' }}>AhaMove</option>
                                <option value="OTHER" {{ old('shipping_provider', $order->shipping_provider) === 'OTHER' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('shipping_provider')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="shipping_status" class="form-label">Trạng Thái Vận Chuyển *</label>
                            <select id="shipping_status" name="shipping_status" class="form-control @error('shipping_status') is-invalid @enderror" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="pending" {{ old('shipping_status', $order->shipping_status) === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="processing" {{ old('shipping_status', $order->shipping_status) === 'processing' ? 'selected' : '' }}>Đang chuẩn bị</option>
                                <option value="shipped" {{ old('shipping_status', $order->shipping_status) === 'shipped' ? 'selected' : '' }}>Đã gửi</option>
                                <option value="out_for_delivery" {{ old('shipping_status', $order->shipping_status) === 'out_for_delivery' ? 'selected' : '' }}>Đang giao hàng</option>
                                <option value="delivered" {{ old('shipping_status', $order->shipping_status) === 'delivered' ? 'selected' : '' }}>Đã giao</option>
                                <option value="returned" {{ old('shipping_status', $order->shipping_status) === 'returned' ? 'selected' : '' }}>Trả lại</option>
                            </select>
                            @error('shipping_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong> Khi cập nhật trạng thái, khách hàng sẽ nhận được email thông báo.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập Nhật Vận Chuyển
                            </button>
                            <a href="{{ route('admin.shipping.orders') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay Lại
                            </a>
                        </div>
                    </form>

                    @if($order->tracking_number)
                        <div class="alert alert-info mt-4">
                            <strong>Thông Tin Hiện Tại:</strong><br>
                            - Mã Tracking: <code>{{ $order->tracking_number }}</code><br>
                            - Nhà Vận Chuyển: {{ $order->shipping_provider }}<br>
                            - Trạng Thái: <span class="badge bg-primary">{{ $order->shipping_status }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
