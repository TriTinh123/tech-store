<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Authentication Code</title>
    <style>
        body { margin: 0; padding: 0; background: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 520px; margin: 40px auto; }
        .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.10); }
        .header { background: linear-gradient(135deg, #3b82f6, #2563eb); padding: 36px 32px; text-align: center; }
        .header h1 { margin: 0; color: #fff; font-size: 22px; font-weight: 700; }
        .header p  { margin: 8px 0 0; color: #bfdbfe; font-size: 14px; }
        .body { padding: 36px 32px; }
        .greeting { font-size: 16px; color: #1e293b; margin-bottom: 16px; }
        .otp-box {
            background: #eff6ff; border: 2px dashed #3b82f6;
            border-radius: 12px; text-align: center; padding: 28px 16px; margin: 24px 0;
        }
        .otp-label { font-size: 13px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .otp-code  { font-size: 42px; font-weight: 800; letter-spacing: 12px; color: #1d4ed8; font-variant-numeric: tabular-nums; }
        .expire { font-size: 13px; color: #ef4444; font-weight: 600; text-align: center; margin-top: 4px; }
        .warning { background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 14px 16px; margin-top: 20px; font-size: 13px; color: #92400e; }
        .footer { background: #f8fafc; padding: 20px 32px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="header">
            <h1>🔐 Two-Factor Authentication (2FA)</h1>
            <p>{{ config('app.name') }} — Adaptive Security System</p>
        </div>
        <div class="body">
            <p class="greeting">
                Hello <strong>{{ $userName ?: 'there' }}</strong>,<br>
                We received a login request for your account.<br>
                Here is your OTP code:
            </p>

            <div class="otp-box">
                <div class="otp-label">OTP Verification Code</div>
                <div class="otp-code">{{ $otpCode }}</div>
            </div>
            <p class="expire">⏳ Code is valid for <strong>10 minutes</strong></p>

            <div class="warning">
                ⚠️ <strong>Security notice:</strong> Never share this code with anyone,
                including support staff. If you did not sign in, please ignore this email
                and change your password immediately.
            </div>
        </div>
        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }} — Automated email, please do not reply.
        </div>
    </div>
</div>
</body>
</html>
