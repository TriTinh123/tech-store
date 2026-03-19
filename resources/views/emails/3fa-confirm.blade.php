<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Login Confirmation</title>
    <style>
        body { margin: 0; padding: 0; background: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrap { max-width: 560px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #ef4444, #dc2626); padding: 36px 32px; text-align: center; color: #fff; }
        .header .icon { font-size: 52px; display: block; margin-bottom: 12px; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .header p  { margin: 8px 0 0; font-size: 14px; opacity: .85; }
        .body { padding: 32px; color: #1e293b; }
        .body p { font-size: 14px; line-height: 1.7; color: #475569; margin: 0 0 16px; }
        .info-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; }
        .info-box .row { display: flex; gap: 8px; font-size: 13px; margin-bottom: 6px; }
        .info-box .label { font-weight: 600; color: #374151; min-width: 120px; }
        .info-box .val   { color: #64748b; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .badge-critical { background: #fee2e2; color: #dc2626; }
        .badge-high     { background: #fef3c7; color: #b45309; }
        .btn-wrap { text-align: center; margin: 24px 0; }
        .btn { display: inline-block; padding: 14px 36px; background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; text-decoration: none; border-radius: 10px; font-size: 15px; font-weight: 700; letter-spacing: .3px; }
        .warning { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 18px; font-size: 13px; color: #92400e; margin-bottom: 16px; }
        .footer { background: #f8fafc; padding: 20px 32px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
        .url-text { word-break: break-all; font-size: 11px; color: #94a3b8; margin-top: 8px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <span class="icon">�</span>
        <h1>Sign-in Confirmation Required</h1>
        <p>We need to verify it's you before allowing access</p>
    </div>

    <div class="body">
        <p>Hello <strong>{{ $userName }}</strong>,</p>
        <p>
            Someone is attempting to sign in to your <strong>{{ config('app.name') }}</strong> account.
            As an extra security step, we need you to confirm this was you.
        </p>

        <div class="info-box">
            <div class="row">
                <span class="label">🌐 IP Address:</span>
                <span class="val">{{ $ipAddress }}</span>
            </div>
            <div class="row">
                <span class="label">🕐 Time:</span>
                <span class="val">{{ now()->format('d/m/Y H:i:s') }} (ICT)</span>
            </div>
        </div>

        <div class="warning">
            ⚠️ <strong>If you did not perform this login</strong>, please ignore this email
            and change your password immediately. The confirmation link expires in <strong>15 minutes</strong>.
        </div>

        <div class="btn-wrap">
            <a href="{{ $confirmUrl }}" class="btn">
                ✅ I Confirm — Allow Sign-In
            </a>
        </div>

        <p class="url-text">Or paste this URL into your browser:<br>{{ $confirmUrl }}</p>
    </div>

    <div class="footer">
        © {{ date('Y') }} {{ config('app.name') }} — This email was sent automatically by the security system.<br>
        Please do not reply to this email.
    </div>
</div>
</body>
</html>
