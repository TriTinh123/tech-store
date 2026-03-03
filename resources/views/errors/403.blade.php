<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Truy cập bị từ chối</title>
    
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
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            text-align: center;
            color: white;
            padding: 2rem;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .error-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .error-message {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .error-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
        }

        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: white;
            color: #3b82f6;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            margin: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🔒</div>
        <div class="error-code">403</div>
        <h1 class="error-title">Truy cập bị từ chối</h1>
        <p class="error-message">
            Bạn không có quyền truy cập trang này. Chỉ quản trị viên (Admin) mới có thể truy cập.
        </p>
        
        <div>
            <a href="{{ route('home') }}" class="btn">🏠 Về trang chủ</a>
            <a href="{{ route('login') }}" class="btn btn-secondary">🔐 Đăng nhập</a>
        </div>
    </div>
</body>
</html>
