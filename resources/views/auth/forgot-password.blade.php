@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-undo"></i> Quên Mật Khẩu?
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Nhập email của bạn để nhận liên kết đặt lại mật khẩu.
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> Kiểm tra lại email:
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('password.send') }}" method="POST" class="form-layout">
                        @csrf

                        <div class="form-group mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email của bạn
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="name@example.com"
                                   required 
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Gửi Liên Kết Đặt Lại
                            </button>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-0">
                                Nhớ mật khẩu? 
                                <a href="{{ route('login') }}" class="btn btn-link p-0">Đăng Nhập</a>
                            </p>
                        </div>

                        <div class="text-center mt-2">
                            <p class="mb-0">
                                Chưa có tài khoản? 
                                <a href="{{ route('register') }}" class="btn btn-link p-0">Đăng Ký</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-info mt-4" role="alert">
                <i class="fas fa-info-circle"></i> 
                <strong>Hướng Dẫn:</strong> Nhập email liên kết với tài khoản của bạn, chúng tôi sẽ gửi liên kết đặt lại mật khẩu đến email của bạn.
            </div>
        </div>
    </div>
</div>

<style>
    .form-label {
        font-weight: 600;
        color: #fa709a;
        margin-bottom: 8px;
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 15px;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: #fa709a;
        box-shadow: 0 0 0 0.2rem rgba(250, 112, 154, 0.25);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .btn {
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .btn-link {
        color: #fa709a;
        text-decoration: none;
    }

    .btn-link:hover {
        text-decoration: underline;
        color: #fee140;
    }

    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header {
        border-bottom: 3px solid #fa709a;
    }

    .text-muted {
        color: #6c757d !important;
        font-size: 0.95rem;
    }
</style>
@endsection
