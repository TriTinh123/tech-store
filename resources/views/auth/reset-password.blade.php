@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="fas fa-lock-open"></i> Đặt Lại Mật Khẩu
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Nhập mật khẩu mới của bạn để hoàn tất quy trình đặt lại.
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
                            <i class="fas fa-exclamation-circle"></i> Kiểm tra lại các trường:
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('password.update') }}" method="POST" class="form-layout">
                        @csrf

                        <input type="hidden" name="token" value="{{ request()->route('token') }}">
                        <input type="hidden" name="email" value="{{ request()->route('email') }}">

                        <div class="form-group mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg" 
                                   id="email" 
                                   value="{{ request()->route('email') }}" 
                                   disabled>
                            <small class="text-muted">Email này không thể thay đổi</small>
                        </div>

                        <div class="form-group mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock-open"></i> Mật Khẩu Mới
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Nhập mật khẩu mới"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Mật khẩu phải có ít nhất 6 ký tự
                            </small>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock-open"></i> Xác Nhận Mật Khẩu
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Xác nhận mật khẩu"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check"></i> Đặt Lại Mật Khẩu
                            </button>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-0">
                                Quay lại? 
                                <a href="{{ route('login') }}" class="btn btn-link p-0">Đăng Nhập</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-warning mt-4" role="alert">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Lưu Ý:</strong> Liên kết này sẽ hết hạn sau 30 phút. Vui lòng hoàn tất quy trình soonest.
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const input = document.getElementById('password');
    const icon = this.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

document.getElementById('toggleConfirm').addEventListener('click', function() {
    const input = document.getElementById('password_confirmation');
    const icon = this.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>

<style>
    .form-label {
        font-weight: 600;
        color: #667eea;
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
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .form-control:disabled {
        background-color: #e9ecef;
        color: #6c757d;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .input-group {
        position: relative;
    }

    .input-group .btn {
        border: 2px solid #e9ecef;
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
        color: #667eea;
        text-decoration: none;
    }

    .btn-link:hover {
        text-decoration: underline;
        color: #764ba2;
    }

    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header {
        border-bottom: 3px solid #667eea;
    }

    .text-muted {
        color: #6c757d !important;
        font-size: 0.9rem;
    }
</style>
@endsection
