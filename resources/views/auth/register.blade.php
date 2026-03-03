<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng Kí - {{ config('app.name', 'Laravel') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .register-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .register-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .register-form {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .form-group input::placeholder {
            color: #999;
        }

        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 38px;
            cursor: pointer;
            font-size: 18px;
            user-select: none;
        }

        .password-group input {
            padding-right: 45px;
        }

        .error-message {
            color: #ff6b6b;
            font-size: 12px;
            margin-top: 5px;
        }

        .terms-group {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
            font-size: 13px;
            color: #666;
        }

        .terms-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            margin-right: 10px;
            cursor: pointer;
            flex-shrink: 0;
            accent-color: #3b82f6;
        }

        .terms-group a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
        }

        .terms-group a:hover {
            text-decoration: underline;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .login-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 700;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            font-size: 12px;
            color: #999;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
            z-index: 0;
        }

        .divider span {
            background: white;
            padding: 0 10px;
            position: relative;
            z-index: 1;
        }

        .social-login {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .social-btn {
            padding: 12px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 48px;
        }

        .social-btn:hover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.05);
        }

        .google-btn {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .google-btn:hover {
            background: #f3f4f6;
            border-color: #bfdbfe;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .success-alert {
            background: #d4edda;
            color: #155724;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            border: 1px solid #c3e6cb;
        }

        @media (max-width: 480px) {
            .register-container {
                max-width: 100%;
            }

            .register-form {
                padding: 30px 20px;
            }

            .register-header {
                padding: 30px 20px;
            }

            .register-header h1 {
                font-size: 24px;
            }
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #1e293b;
            color: #94a3b8;
            padding: 40px 20px;
            text-align: center;
            font-size: 13px;
            line-height: 1.8;
            border-top: 1px solid #334155;
        }

        .footer p {
            margin: 8px 0;
        }

        .footer strong {
            color: #e2e8f0;
            font-weight: 600;
        }

        body {
            padding-bottom: 200px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Header -->
        <div class="register-header">
            <h1>🎉 Tạo Tài Khoản</h1>
            <p>Tham gia cộng đồng {{ config('app.name', 'Shop') }} ngay hôm nay</p>
        </div>

        <!-- Form -->
        <form class="register-form" action="{{ route('register') }}" method="POST">
            @csrf

            @if (session('success'))
                <div style="background: #d4edda; color: #155724; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid #c3e6cb;">
                    <strong>✓ Thành công!</strong> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="background: #f8d7da; color: #721c24; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid #f5c6cb;">
                    <strong>Lỗi!</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Full Name -->
            <div class="form-group">
                <label for="name">Họ và tên</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    placeholder="Nguyễn Văn A"
                    value="{{ old('name') }}"
                    required
                >
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="your@email.com"
                    value="{{ old('email') }}"
                    required
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group password-group">
                <label for="password">Mật khẩu</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="••••••••"
                    required
                >
                <span class="password-toggle" onclick="togglePassword('password')">👁️</span>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group password-group">
                <label for="password_confirmation">Xác nhận mật khẩu</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    placeholder="••••••••"
                    required
                >
                <span class="password-toggle" onclick="togglePassword('password_confirmation')">👁️</span>
            </div>

            <!-- Terms & Conditions -->
            <div class="terms-group">
                <input type="checkbox" id="terms" name="terms" value="1" required>
                <label for="terms">
                    Tôi đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách bảo mật</a> của {{ config('app.name', 'Shop') }}
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn">
                Tạo Tài Khoản
            </button>

            <!-- Login Link -->
            <div class="login-link">
                Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập tại đây</a>
            </div>

            <!-- Divider -->
            <div class="divider">
                <span>Hoặc tiếp tục với</span>
            </div>

            <!-- Google Login -->
            <button type="button" class="google-btn" onclick="loginWithGoogle()">
                <svg style="width: 18px; height: 18px; margin-right: 8px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC04"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Continue with Google
            </button>
        </form>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const span = event.target;
            
            if (input.type === 'password') {
                input.type = 'text';
                span.textContent = '🙈';
            } else {
                input.type = 'password';
                span.textContent = '👁️';
            }
        }

        function loginWithGoogle() {
            window.location.href = "{{ route('auth.google') }}";
        }
    </script>

    <footer class="footer">
        <p><strong>© 2026 TechStore.</strong> All rights reserved.</p>
        <p>Enhanced with traditional web security mechanisms (CSRF, XSS, rate limiting) and AI-based login anomaly detection with Three-Factor Authentication (3FA).</p>
    </footer>
</body>
</html>
