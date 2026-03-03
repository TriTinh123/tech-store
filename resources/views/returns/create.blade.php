@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-undo"></i> Yêu Cầu Hoàn Trả Hàng
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('returns.store', $order->id) }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="order_item_id" class="form-label">Sản phẩm cần hoàn trả *</label>
                            <select id="order_item_id" name="order_item_id" class="form-control @error('order_item_id') is-invalid @enderror" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->product_name }} - ₫{{ number_format($item->subtotal, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('order_item_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="reason" class="form-label">Lý do hoàn trả *</label>
                            <select id="reason" name="reason" class="form-control @error('reason') is-invalid @enderror" required>
                                <option value="">-- Chọn lý do --</option>
                                <option value="defective">Sản phẩm bị lỗi/hư hỏng</option>
                                <option value="wrong_item">Gửi nhầm sản phẩm</option>
                                <option value="not_as_described">Sản phẩm không đúng mô tả</option>
                                <option value="changed_mind">Tôi thay đổi ý định</option>
                                <option value="other">Lý do khác</option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Mô tả chi tiết *</label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="5" placeholder="Vui lòng mô tả chi tiết lý do hoàn trả..." required></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong> Sau khi gửi yêu cầu, chúng tôi sẽ xem xét trong vòng 24-48 giờ và liên hệ bạn.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Gửi yêu cầu hoàn trả
                            </button>
                            <a href="{{ route('profile.order-detail', $order->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
