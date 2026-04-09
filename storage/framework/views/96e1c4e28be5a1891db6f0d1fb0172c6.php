<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Verification — <?php echo e(config('app.name')); ?></title>
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet"/>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
        body{background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{width:100%;max-width:540px;background:#0f172a;border-radius:20px;box-shadow:0 24px 64px rgba(0,0,0,.6);border:1px solid #1e293b;overflow:hidden}

        /* Header */
        .header{padding:32px;text-align:center;border-bottom:1px solid #1e293b}
        .header .icon-wrap{width:72px;height:72px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:34px}
        .header h1{font-size:20px;font-weight:700;color:#f1f5f9;margin-bottom:6px}
        .header p{font-size:13px;color:#94a3b8}

        /* Risk badge */
        .badge{display:inline-block;padding:4px 14px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;margin-bottom:8px}
        .badge-critical{background:#7f1d1d;color:#fca5a5}
        .badge-high    {background:#9a3412;color:#fed7aa}
        .badge-medium  {background:#854d0e;color:#fde68a}
        .badge-low     {background:#166534;color:#86efac}

        /* Body */
        .body{padding:28px 32px}

        /* Order summary box */
        .order-box{background:#1e293b;border-radius:12px;padding:20px;margin-bottom:20px;border:1px solid #334155}
        .order-box .amount{font-size:28px;font-weight:800;color:#f97316;margin-bottom:4px}
        .order-box .label{font-size:12px;color:#64748b}

        /* Risk bar */
        .risk-bar-wrap{display:flex;align-items:center;gap:10px;margin:12px 0 0}
        .risk-bar-track{flex:1;height:8px;background:#334155;border-radius:4px;overflow:hidden}
        .risk-bar-fill{height:100%;border-radius:4px;transition:width .4s}
        .risk-bar-label{font-size:12px;font-weight:700;min-width:30px;text-align:right}

        /* Reasons list */
        .reasons{background:#1e293b;border-radius:10px;padding:16px 18px;margin-bottom:20px;border-left:3px solid #f59e0b}
        .reasons h4{font-size:12px;font-weight:700;color:#fbbf24;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px}
        .reasons ul{list-style:none}
        .reasons li{font-size:13px;color:#e2e8f0;padding:4px 0;display:flex;gap:8px;align-items:flex-start;line-height:1.5}
        .reasons li::before{content:'⚠';font-size:12px;flex-shrink:0;margin-top:1px}

        /* OTP form */
        .otp-section label{display:block;font-size:13px;font-weight:600;color:#cbd5e1;margin-bottom:8px}
        .otp-input{width:100%;padding:14px 16px;background:#1e293b;border:2px solid #334155;border-radius:10px;color:#f1f5f9;font-size:22px;font-weight:700;letter-spacing:6px;text-align:center;outline:none;transition:border-color .2s}
        .otp-input:focus{border-color:#f97316}
        .otp-input::placeholder{letter-spacing:2px;font-size:16px;font-weight:400;color:#475569}
        .error-msg{color:#f87171;font-size:12px;margin-top:6px}

        /* Buttons */
        .btn-verify{width:100%;padding:14px;background:linear-gradient(135deg,#ea580c,#dc2626);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;margin-top:16px;transition:opacity .2s}
        .btn-verify:hover{opacity:.9}
        .btn-verify:disabled{opacity:.5;cursor:not-allowed}
        .btn-resend{width:100%;padding:11px;background:transparent;color:#94a3b8;border:1px solid #334155;border-radius:10px;font-size:13px;cursor:pointer;margin-top:10px;transition:all .2s}
        .btn-resend:hover{border-color:#64748b;color:#e2e8f0}

        /* Countdown */
        .countdown{text-align:center;font-size:12px;color:#64748b;margin-top:12px}
        .countdown span{color:#f97316;font-weight:700}

        /* Flash messages */
        .flash-warning{background:#451a03;border:1px solid #92400e;color:#fde68a;padding:14px 16px;border-radius:10px;font-size:13px;margin-bottom:20px}
        .flash-info   {background:#1e3a5f;border:1px solid #2563eb;color:#93c5fd;padding:14px 16px;border-radius:10px;font-size:13px;margin-bottom:20px}
        .flash-error  {background:#450a0a;border:1px solid #991b1b;color:#fca5a5;padding:14px 16px;border-radius:10px;font-size:13px;margin-bottom:20px}

        /* Footer note */
        .footer-note{text-align:center;font-size:12px;color:#475569;padding:20px 32px;border-top:1px solid #1e293b}
        .footer-note a{color:#64748b;text-decoration:underline}
    </style>
</head>
<body>
<div class="card">

    
    <?php
        $level = $fraud['risk_level'] ?? 'medium';
        $headerColors = [
            'critical' => ['from'=>'#7f1d1d','to'=>'#991b1b','icon'=>'🚨'],
            'high'     => ['from'=>'#9a3412','to'=>'#c2410c','icon'=>'⚠️'],
            'medium'   => ['from'=>'#854d0e','to'=>'#b45309','icon'=>'🔶'],
            'low'      => ['from'=>'#166534','to'=>'#15803d','icon'=>'🔒'],
        ];
        $hc = $headerColors[$level] ?? $headerColors['high'];
        $barColor = ['critical'=>'#ef4444','high'=>'#f97316','medium'=>'#eab308','low'=>'#22c55e'][$level] ?? '#f97316';
    ?>

    <div class="header" style="background:linear-gradient(135deg,#1e3a5f,#1e40af)">
        <div class="icon-wrap" style="background:rgba(255,255,255,.15)">🔐</div>
        <h1>Order Verification Required</h1>
        <p>To protect your account, please verify this order.<br>
           Enter the OTP sent to your email to continue.</p>
    </div>

    
    <div class="body">

        
        <?php if(session('warning')): ?>
            <div class="flash-warning"><?php echo e(session('warning')); ?></div>
        <?php endif; ?>
        <?php if(session('info')): ?>
            <div class="flash-info"><?php echo e(session('info')); ?></div>
        <?php endif; ?>
        <?php if($errors->has('otp_code')): ?>
            <div class="flash-error"><?php echo e($errors->first('otp_code')); ?></div>
        <?php endif; ?>

        
        <div class="order-box">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
                <div>
                    <div class="label">Order Amount</div>
                    <div class="amount">$<?php echo e(number_format($total, 2)); ?></div>
                </div>
                <div style="text-align:right">
                    <div class="label">Customer</div>
                    <div style="font-size:14px;font-weight:600;color:#e2e8f0;margin-top:2px">
                        <?php echo e($user->name); ?>

                    </div>
                    <div style="font-size:11px;color:#64748b"><?php echo e($user->email); ?></div>
                </div>
            </div>


        </div>

        
        <form method="POST" action="<?php echo e(route('checkout.verify-purchase.submit')); ?>" id="otpForm">
            <?php echo csrf_field(); ?>
            <div class="otp-section">
                <label for="otp_code">
                    🔑 Enter OTP code (sent to <?php echo e($user->email); ?>)
                </label>
                <input
                    type="text"
                    id="otp_code"
                    name="otp_code"
                    class="otp-input"
                    placeholder="• • • • • •"
                    maxlength="6"
                    inputmode="numeric"
                    pattern="[0-9]{6}"
                    autocomplete="one-time-code"
                    autofocus
                >
            </div>

            <button type="submit" class="btn-verify" id="submitBtn">
                Verify & Place Order
            </button>
        </form>

        
        <form method="POST" action="<?php echo e(route('checkout.verify-purchase.resend')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn-resend">
                📧 Resend OTP
            </button>
        </form>

        
        <div class="countdown">
            Code expires in <span id="timer">10:00</span>
        </div>

    </div>

    
    <div class="footer-note">
        If you did not place this order, please
        <a href="<?php echo e(route('login')); ?>">change your password immediately</a>
        or contact support.
    </div>
</div>

<script>
    // Auto-format OTP input (numbers only)
    document.getElementById('otp_code').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
        document.getElementById('submitBtn').disabled = this.value.length < 6;
    });
    document.getElementById('submitBtn').disabled = true;

    // 10-minute countdown
    let remaining = 600;
    const timerEl = document.getElementById('timer');
    const tick = setInterval(() => {
        remaining--;
        const m = Math.floor(remaining / 60).toString().padStart(2, '0');
        const s = (remaining % 60).toString().padStart(2, '0');
        timerEl.textContent = `${m}:${s}`;
        if (remaining <= 60) timerEl.style.color = '#ef4444';
        if (remaining <= 0) {
            clearInterval(tick);
            timerEl.textContent = 'Expired';
            document.getElementById('submitBtn').disabled = true;
        }
    }, 1000);
</script>
</body>
</html>
<?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/purchase-risk-challenge.blade.php ENDPATH**/ ?>