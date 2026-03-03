@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-edit"></i> Chỉnh Sửa Mã Giảm Giá
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info">
                            <strong>Mã:</strong> {{ $coupon->code }}
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Mô Tả</label>
                            <textarea id="description" name="description" class="form-control" rows="2">{{ old('description', $coupon->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="type" class="form-label">Loại *</label>
                                    <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Cố định (VND)</option>
                                        <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="value" class="form-label">Giá Trị *</label>
                                    <input type="number" id="value" name="value" class="form-control @error('value') is-invalid @enderror" 
                                           value="{{ old('value', $coupon->value) }}" step="0.01" required>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="max_discount" class="form-label">Giảm Giá Tối Đa (VND)</label>
                                    <input type="number" id="max_discount" name="max_discount" class="form-control" 
                                           value="{{ old('max_discount', $coupon->max_discount) }}" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="min_order_amount" class="form-label">Đơn Hàng Tối Thiểu (VND) *</label>
                                    <input type="number" id="min_order_amount" name="min_order_amount" class="form-control @error('min_order_amount') is-invalid @enderror" 
                                           value="{{ old('min_order_amount', $coupon->min_order_amount) }}" step="0.01" required>
                                    @error('min_order_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="usage_limit" class="form-label">Giới Hạn Sử Dụng</label>
                            <div class="input-group">
                                <input type="number" id="usage_limit" name="usage_limit" class="form-control" 
                                       value="{{ old('usage_limit', $coupon->usage_limit) }}" min="0">
                                <span class="input-group-text">Đã dùng: {{ $coupon->used_count }}</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="valid_from" class="form-label">Ngày Bắt Đầu *</label>
                                    <input type="date" id="valid_from" name="valid_from" class="form-control @error('valid_from') is-invalid @enderror" 
                                           value="{{ old('valid_from', $coupon->valid_from->format('Y-m-d')) }}" required>
                                    @error('valid_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="valid_to" class="form-label">Ngày Kết Thúc *</label>
                                    <input type="date" id="valid_to" name="valid_to" class="form-control @error('valid_to') is-invalid @enderror" 
                                           value="{{ old('valid_to', $coupon->valid_to->format('Y-m-d')) }}" required>
                                    @error('valid_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" id="is_active" name="is_active" class="form-check-input" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Kích hoạt
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập Nhật
                            </button>
                            <a href="{{ route('admin.coupons') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay Lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
