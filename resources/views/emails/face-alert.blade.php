<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Alert — Unrecognized Face</title>
    <style>
        body { margin: 0; padding: 0; background: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrap { max-width: 560px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #7c3aed, #dc2626); padding: 36px 32px; text-align: center; color: #fff; }
        .header .icon { font-size: 56px; display: block; margin-bottom: 12px; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .header p  { margin: 8px 0 0; font-size: 14px; opacity: .85; }
        .body { padding: 32px; color: #1e293b; }
        .body p { font-size: 14px; line-height: 1.7; color: #475569; margin: 0 0 16px; }
        .info-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; }
        .info-box .row { display: flex; gap: 8px; font-size: 13px; margin-bottom: 8px; }
        .info-box .row:last-child { margin-bottom: 0; }
        .info-box .label { font-weight: 600; color: #374151; min-width: 130px; flex-shrink: 0; }
        .info-box .val   { color: #64748b; word-break: break-word; }
        .alert-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 16px 18px; font-size: 13px; color: #92400e; margin-bottom: 24px; line-height: 1.6; }
        .action-box { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 16px 18px; font-size: 13px; color: #7f1d1d; margin-bottom: 24px; line-height: 1.6; }
        .action-box ul { margin: 8px 0 0 18px; padding: 0; }
        .action-box li { margin-bottom: 4px; }
        .footer { background: #f8fafc; padding: 20px 32px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <span class="icon">🚨</span>
        <h1>Unrecognized Face Login Attempt</h1>
        <p>An unknown person tried to access your account</p>
    </div>

    <div class="body">
        <p>Hello <strong>{{ $userName }}</strong>,</p>
        <p>
            The security system of <strong>{{ config('app.name') }}</strong> detected a
            <strong>face recognition failure</strong> during a login attempt to your account.
            The face captured by the camera did <strong>not match</strong> your registered face profile.
        </p>

        <div class="info-box">
            <div class="row">
                <span class="label">📧 Account:</span>
                <span class="val">{{ $userEmail }}</span>
            </div>
            <div class="row">
                <span class="label">🌐 IP Address:</span>
                <span class="val">{{ $ipAddress }}</span>
            </div>
            <div class="row">
                <span class="label">🖥️ Device / Browser:</span>
                <span class="val">{{ $userAgent }}</span>
            </div>
            <div class="row">
                <span class="label">🕐 Time:</span>
                <span class="val">{{ $attemptedAt }}</span>
            </div>
        </div>

        <div class="alert-box">
            ⚠️ <strong>Was this you?</strong><br>
            If you were trying to log in and see this email, it may mean your face data has changed
            (different lighting, glasses, or a new face profile is needed). Please log in using your
            Security Question or Email Confirmation method and re-enroll your face.
        </div>

        <div class="action-box">
            🔒 <strong>If this was NOT you:</strong>
            <ul>
                <li>Change your password immediately</li>
                <li>Enable a stronger authentication method</li>
                <li>Contact our support team if you suspect unauthorized access</li>
            </ul>
        </div>

        <p style="font-size:13px;color:#64748b;">
            The login attempt was <strong>blocked</strong>. Your account remains secure.
            Our AI security system flagged this session and prevented unauthorized access.
        </p>
    </div>

    <div class="footer">
        © {{ date('Y') }} {{ config('app.name') }} — This is an automated security notification.<br>
        Please do not reply to this email.
    </div>
</div>
</body>
</html>
