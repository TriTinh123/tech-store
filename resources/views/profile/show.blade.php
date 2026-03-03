@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-user"></i> Hồ Sơ Cá Nhân
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> Có lỗi xảy ra!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="profile-info">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="label" style="color: #667eea; font-weight: bold;">Tên:</span>
                                    <span class="value">{{ $user->name }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="label" style="color: #667eea; font-weight: bold;">Email:</span>
                                    <span class="value">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="label" style="color: #667eea; font-weight: bold;">Điện thoại:</span>
                                    <span class="value">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="label" style="color: #667eea; font-weight: bold;">Vai trò:</span>
                                    <span class="badge bg-info">{{ $user->role == 'admin' ? 'Quản trị viên' : 'Người dùng' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="info-item">
                                    <span class="label" style="color: #667eea; font-weight: bold;">Địa chỉ:</span>
                                    <span class="value">{{ $user->address ?? 'Chưa cập nhật' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="info-item">
                                    <span class="label" style="color: #667eea; font-weight: bold;">Ngày tạo tài khoản:</span>
                                    <span class="value">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-edit"></i> Sửa Thông Tin
                        </a>
                        <a href="{{ route('profile.change-password') }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-key"></i> Đổi Mật Khẩu
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-info btn-lg">
                            <i class="fas fa-shopping-bag"></i> Lịch Sử Đơn Hàng
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Quay Lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-item {
        padding: 12px 0;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .label {
        font-weight: 600;
        min-width: 150px;
    }

    .value {
        color: #495057;
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header {
        border-bottom: 3px solid #667eea;
    }
</style>
@endsection
