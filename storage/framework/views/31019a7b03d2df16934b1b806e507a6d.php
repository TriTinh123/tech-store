<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px;
            color: #333;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .otp-box {
            background-color: #f0f7ff;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 10px;
            font-family: 'Courier New', monospace;
            margin: 0;
        }
        .otp-expiry {
            color: #666;
            font-size: 14px;
            margin-top: 15px;
        }
        .info-text {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin: 20px 0;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #856404;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔐 Xác Thực 3FA</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Xin chào <?php echo e($userName ?? 'người dùng'); ?>,
            </div>

            <p class="info-text">
                Bạn vừa yêu cầu xác thực ba yếu tố (3FA) cho tài khoản của mình. 
                Dưới đây là mã OTP (One-Time Password) của bạn:
            </p>

            <!-- OTP Box -->
            <div class="otp-box">
                <p class="otp-code"><?php echo e($otp); ?></p>
                <p class="otp-expiry">
                    ⏱️ Mã này sẽ hết hạn sau <strong><?php echo e($validMinutes); ?> phút</strong>
                </p>
            </div>

            <!-- Instructions -->
            <p class="info-text">
                Hãy nhập mã OTP này vào ứng dụng để hoàn tất quá trình xác thực. 
                Nếu bạn không yêu cầu xác thực này, vui lòng bỏ qua email này.
            </p>

            <!-- Warning -->
            <div class="warning">
                <strong>⚠️ Cảnh báo bảo mật:</strong><br>
                · Không bao giờ chia sẻ mã OTP này với bất kỳ ai<br>
                · Chúng tôi sẽ không bao giờ yêu cầu mã OTP qua email<br>
                · Nếu không phải bạn yêu cầu, vui lòng đổi mật khẩu ngay
            </div>

            <!-- Additional Info -->
            <p class="info-text">
                Nếu bạn gặp bất kỳ vấn đề nào, vui lòng liên hệ với chúng tôi 
                qua support@example.com hoặc truy cập trang hỗ trợ của chúng tôi.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                © <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. Mọi quyền được bảo lưu.
            </p>
            <p>
                Đây là email tự động, vui lòng không trả lời email này.
            </p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/emails/otp.blade.php ENDPATH**/ ?>