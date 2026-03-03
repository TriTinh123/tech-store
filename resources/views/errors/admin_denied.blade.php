@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body text-center py-5">
                    <i class="fas fa-lock" style="font-size: 3rem; color: #e74c3c; margin-bottom: 20px; display: block;"></i>
                    <h1 class="mb-3" style="color: #2c3e50;">Không có quyền truy cập</h1>
                    <p class="text-muted mb-4" style="font-size: 1.1rem;">
                        Bạn không phải Admin. Chỉ tài khoản Admin mới có thể truy cập trang này.
                    </p>
                    <hr>
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-home"></i> Quay Lại Trang Chủ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }
</style>
@endsection
