@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-plus-circle"></i> Tạo Mã Giảm Giá Mới
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.coupons.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="code" class="form-label">Mã Giảm Giá *</label>
                            <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" 
                                   placeholder="VD: SUMMER20" value="{{ old('code') }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Mô Tả</label>
                            <textarea id="description" name="description" class="form-control" rows="2" placeholder="Mô tả mã giảm giá">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="type" class="form-label">Loại *</label>
                                    <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="">-- Chọn loại --</option>
                                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Cố định (VND)</option>
                                        <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
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
                                           placeholder="Nhập giá trị" value="{{ old('value') }}" step="0.01" required>
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
                                           placeholder="Tối đa có thể giảm" value="{{ old('max_discount') }}" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="min_order_amount" class="form-label">Đơn Hàng Tối Thiểu (VND) *</label>
                                    <input type="number" id="min_order_amount" name="min_order_amount" class="form-control @error('min_order_amount') is-invalid @enderror" 
                                           placeholder="Giá trị đơn tối thiểu" value="{{ old('min_order_amount', 0) }}" step="0.01" required>
                                    @error('min_order_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="usage_limit" class="form-label">Giới Hạn Sử Dụng (Để trống = không giới hạn)</label>
                            <input type="number" id="usage_limit" name="usage_limit" class="form-control" 
                                   placeholder="Số lần có thể sử dụng" value="{{ old('usage_limit') }}" min="0">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="valid_from" class="form-label">Ngày Bắt Đầu *</label>
                                    <input type="date" id="valid_from" name="valid_from" class="form-control @error('valid_from') is-invalid @enderror" 
                                           value="{{ old('valid_from') }}" required>
                                    @error('valid_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="valid_to" class="form-label">Ngày Kết Thúc *</label>
                                    <input type="date" id="valid_to" name="valid_to" class="form-control @error('valid_to') is-invalid @enderror" 
                                           value="{{ old('valid_to') }}" required>
                                    @error('valid_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" id="is_active" name="is_active" class="form-check-input" {{ old('is_active') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Kích hoạt ngay
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Tạo Mã Giảm Giá
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
