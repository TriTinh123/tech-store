<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>OTP Verification — <?php echo e(config('app.name')); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;background:#0a0a1a;position:relative;overflow:hidden}

/* Animated background */
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 80% 60% at 20% 20%,rgba(99,102,241,.18) 0%,transparent 60%),radial-gradient(ellipse 60% 60% at 80% 80%,rgba(139,92,246,.15) 0%,transparent 60%),radial-gradient(ellipse 50% 40% at 60% 10%,rgba(59,130,246,.12) 0%,transparent 50%);pointer-events:none}
.bg-blob{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;animation:floatBlob 8s ease-in-out infinite alternate}
.bg-blob-1{width:500px;height:500px;background:rgba(99,102,241,.07);top:-100px;left:-100px;animation-delay:0s}
.bg-blob-2{width:400px;height:400px;background:rgba(139,92,246,.07);bottom:-100px;right:-100px;animation-delay:3s}
.bg-blob-3{width:300px;height:300px;background:rgba(56,189,248,.05);top:40%;left:60%;animation-delay:1.5s}
@keyframes floatBlob{0%{transform:translate(0,0) scale(1)}100%{transform:translate(30px,20px) scale(1.08)}}

/* Grid dots pattern */
.grid-pattern{position:fixed;inset:0;background-image:radial-gradient(rgba(255,255,255,.04) 1px,transparent 1px);background-size:32px 32px;pointer-events:none}

/* Card */
.card{position:relative;width:100%;max-width:440px;background:rgba(255,255,255,.03);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid rgba(255,255,255,.08);border-radius:24px;padding:28px 32px;box-shadow:0 32px 80px rgba(0,0,0,.5),inset 0 1px 0 rgba(255,255,255,.06);overflow:hidden}

/* Top glow line */
.card::before{content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);width:60%;height:1px;background:linear-gradient(90deg,transparent,rgba(99,102,241,.8),transparent);border-radius:50%}

/* Header */
.logo-wrap{display:flex;align-items:center;justify-content:center;margin-bottom:16px}
.logo-icon{width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;box-shadow:0 12px 30px rgba(99,102,241,.5),0 0 0 1px rgba(99,102,241,.3);position:relative}
.logo-icon::after{content:'';position:absolute;inset:-5px;border-radius:21px;border:1px solid rgba(99,102,241,.25);animation:pulse 2.5s ease-in-out infinite}
@keyframes pulse{0%,100%{opacity:.5;transform:scale(1)}50%{opacity:1;transform:scale(1.05)}}
.logo-icon i{font-size:22px;color:#fff}
.badge{position:absolute;top:-5px;right:-5px;background:#22c55e;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:9px;color:#fff;border:2px solid #0a0a1a}

.card-title{text-align:center;margin-bottom:6px}
.card-title h1{font-size:22px;font-weight:800;color:#fff;letter-spacing:-.5px}
.card-title p{font-size:13px;color:rgba(255,255,255,.45);margin-top:5px;line-height:1.5}

/* Alerts */
.alert{border-radius:12px;padding:13px 16px;font-size:13px;margin-bottom:16px;display:flex;align-items:flex-start;gap:10px;border:1px solid transparent}
.alert-info{background:rgba(59,130,246,.1);border-color:rgba(59,130,246,.25);color:#93c5fd}
.alert-info i{color:#60a5fa;margin-top:1px;flex-shrink:0}
.alert-danger{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.25);color:#fca5a5}
.alert-danger i{color:#f87171;margin-top:1px;flex-shrink:0}

/* Info row */
.info-row{background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);border-radius:12px;padding:10px 14px;font-size:12px;color:rgba(255,255,255,.55);margin-bottom:16px;display:flex;align-items:center;gap:10px}
.info-row i{color:#818cf8;flex-shrink:0}
.info-row strong{color:#a5b4fc}

/* OTP */
.otp-label{font-size:11px;font-weight:700;color:rgba(255,255,255,.35);letter-spacing:1.2px;text-transform:uppercase;margin-bottom:10px;display:block}
.otp-grid{display:flex;gap:7px;width:100%}
.otp-box{flex:1;min-width:0;width:0;height:52px;background:rgba(255,255,255,.04);border:1.5px solid rgba(255,255,255,.1);border-radius:10px;font-size:22px;font-weight:800;text-align:center;color:#fff;font-family:'Inter',sans-serif;caret-color:transparent;outline:none;transition:all .2s;-webkit-appearance:none;appearance:none}
.otp-box:focus{background:rgba(99,102,241,.12);border-color:rgba(99,102,241,.6);box-shadow:0 0 0 4px rgba(99,102,241,.15)}
.otp-box.filled{background:rgba(99,102,241,.1);border-color:rgba(99,102,241,.5);color:#c7d2fe}
.otp-box.done{background:rgba(34,197,94,.08);border-color:rgba(34,197,94,.45);color:#86efac}
.field-error{font-size:12px;color:#f87171;margin-top:8px;display:flex;align-items:center;gap:6px}

/* Timer */
.timer-row{display:flex;align-items:center;justify-content:space-between;margin:12px 0 16px}
.timer-label{font-size:13px;color:rgba(255,255,255,.35);display:flex;align-items:center;gap:7px}
.timer-label i{font-size:12px}
.timer-badge{font-size:13px;font-weight:700;padding:4px 14px;border-radius:20px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);color:#4ade80;font-variant-numeric:tabular-nums;transition:all .4s}
.timer-badge.urgent{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.25);color:#f87171}

/* Verify button */
.btn-verify{width:100%;padding:13px;border:none;border-radius:12px;font-size:14px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px;transition:all .3s;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;box-shadow:0 8px 28px rgba(99,102,241,.35);margin-bottom:12px;letter-spacing:.2px}
.btn-verify:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 14px 36px rgba(99,102,241,.5);filter:brightness(1.08)}
.btn-verify:active:not(:disabled){transform:translateY(0)}
.btn-verify:disabled{opacity:.35;cursor:not-allowed;filter:none;transform:none;box-shadow:none}

/* Resend */
.divider{display:flex;align-items:center;gap:12px;margin-bottom:10px}
.div-line{flex:1;height:1px;background:rgba(255,255,255,.07)}
.div-text{font-size:12px;color:rgba(255,255,255,.2);white-space:nowrap}
.btn-resend{width:100%;padding:11px;background:rgba(255,255,255,.04);border:1.5px solid rgba(255,255,255,.08);border-radius:12px;font-size:13px;font-weight:600;color:rgba(255,255,255,.55);cursor:pointer;font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .3s;margin-bottom:14px}
.btn-resend:hover{background:rgba(99,102,241,.1);border-color:rgba(99,102,241,.3);color:#a5b4fc}

/* Back */
.back-row{text-align:center}
.back-row a{font-size:13px;color:rgba(255,255,255,.3);text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:color .2s}
.back-row a:hover{color:#a5b4fc}

/* Shake */
@keyframes shake{0%,100%{transform:translateX(0)}20%{transform:translateX(-7px)}40%{transform:translateX(7px)}60%{transform:translateX(-4px)}80%{transform:translateX(4px)}}
.shake{animation:shake .4s ease}

/* Success overlay */
.success-overlay{display:none;position:absolute;inset:0;border-radius:28px;background:rgba(10,10,26,.92);backdrop-filter:blur(8px);z-index:10;flex-direction:column;align-items:center;justify-content:center;gap:16px}
.success-overlay.show{display:flex}
.s-ring{width:80px;height:80px;border-radius:50%;border:3px solid rgba(34,197,94,.25);display:flex;align-items:center;justify-content:center;animation:ringPop .5s cubic-bezier(.34,1.56,.64,1) forwards}
@keyframes ringPop{0%{transform:scale(.4);opacity:0}100%{transform:scale(1);opacity:1}}
.s-ring::before{content:'';position:absolute;width:80px;height:80px;border-radius:50%;border:2px solid rgba(34,197,94,.15);animation:ringExpand .8s ease-out .3s forwards;opacity:0}
@keyframes ringExpand{0%{transform:scale(1);opacity:.8}100%{transform:scale(1.6);opacity:0}}
.s-icon{width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#22c55e,#16a34a);display:flex;align-items:center;justify-content:center;box-shadow:0 8px 30px rgba(34,197,94,.5);animation:iconPop .5s cubic-bezier(.34,1.56,.64,1) .1s both}
@keyframes iconPop{0%{transform:scale(.3);opacity:0}100%{transform:scale(1);opacity:1}}
.s-icon i{font-size:28px;color:#fff}
.s-title{font-size:20px;font-weight:800;color:#fff;animation:fadeUp .4s ease .25s both}
.s-sub{font-size:13px;color:rgba(255,255,255,.4);animation:fadeUp .4s ease .35s both;display:flex;align-items:center;gap:8px}
@keyframes fadeUp{0%{transform:translateY(10px);opacity:0}100%{transform:translateY(0);opacity:1}}
.s-dots span{display:inline-block;width:6px;height:6px;border-radius:50%;background:rgba(34,197,94,.6);animation:dot 1.2s ease-in-out infinite}
.s-dots span:nth-child(2){animation-delay:.2s}
.s-dots span:nth-child(3){animation-delay:.4s}
@keyframes dot{0%,80%,100%{transform:scale(.6);opacity:.4}40%{transform:scale(1);opacity:1}}
</style>
</head>
<body>
<div class="bg-blob bg-blob-1"></div>
<div class="bg-blob bg-blob-2"></div>
<div class="bg-blob bg-blob-3"></div>
<div class="grid-pattern"></div>

<div class="card">
    <div class="logo-wrap">
        <div class="logo-icon">
            <i class="fa-solid fa-shield-halved"></i>
            <div class="badge"><i class="fa-solid fa-check" style="font-size:8px"></i></div>
        </div>
    </div>

    <div class="card-title">
        <h1>OTP Verification</h1>
        <p>Enter the 6-digit code sent to your<br>registered email address</p>
    </div>

    <?php if(session('info')): ?>
    <div class="alert alert-info">
        <i class="fa-solid fa-circle-info"></i>
        <span><?php echo e(session('info')); ?></span>
    </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
    <div class="alert alert-danger" id="errAlert">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($error); ?><br><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
    </div>
    <?php endif; ?>

    <div class="info-row">
        <i class="fa-solid fa-lock"></i>
        <span>OTP is valid for <strong>10 minutes</strong>. Check inbox or spam folder.</span>
    </div>

    <form method="POST" action="<?php echo e(route('auth.otp.verify')); ?>" id="otpForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="otp_code" id="otpCode">

        <label class="otp-label">6-digit OTP code</label>
        <div class="otp-grid" id="otpGrid">
            <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
            <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
            <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
            <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
            <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
            <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
        </div>
        <?php $__errorArgs = ['otp_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="field-error"><i class="fa-solid fa-triangle-exclamation"></i><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <div class="timer-row">
            <span class="timer-label"><i class="fa-regular fa-clock"></i> Code expires in</span>
            <span class="timer-badge" id="countdown">10:00</span>
        </div>

        <button type="submit" class="btn-verify" id="verifyBtn" disabled onclick="doVerify()">
            <i class="fa-solid fa-circle-check"></i> Verify OTP
        </button>
    </form>

    <div class="divider">
        <div class="div-line"></div>
        <span class="div-text">Didn't receive the code?</span>
        <div class="div-line"></div>
    </div>

    <form method="POST" action="<?php echo e(route('auth.otp.resend')); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn-resend">
            <i class="fa-solid fa-rotate-right"></i> Resend OTP
        </button>
    </form>

    <div class="back-row">
        <a href="<?php echo e(route('login')); ?>"><i class="fa-solid fa-arrow-left"></i> Back to login</a>
    </div>

    <!-- Success overlay -->
    <div class="success-overlay" id="successOverlay">
        <div class="s-ring">
            <div class="s-icon"><i class="fa-solid fa-check"></i></div>
        </div>
        <div class="s-title">OTP Verified!</div>
        <div class="s-sub">
            Redirecting you
            <div class="s-dots"><span></span><span></span><span></span></div>
        </div>
    </div>
</div>

<script>
(function(){
    var boxes = Array.from(document.querySelectorAll('.otp-box'));
    var hidden = document.getElementById('otpCode');
    var btn    = document.getElementById('verifyBtn');
    var form   = document.getElementById('otpForm');
    var grid   = document.getElementById('otpGrid');

    function sync(){
        var val = boxes.map(function(b){return b.value}).join('');
        hidden.value = val;
        var full = val.length === 6;
        btn.disabled = !full;
        boxes.forEach(function(b){ b.classList.toggle('filled', b.value !== ''); });
        if(full){
            boxes.forEach(function(b){ b.classList.add('done'); b.classList.remove('filled'); });
        }
    }

    boxes.forEach(function(box, i){
        box.addEventListener('input', function(){
            var v = this.value.replace(/\D/g,'');
            this.value = v ? v[v.length-1] : '';
            if(this.value && i < 5) boxes[i+1].focus();
            sync();
        });
        box.addEventListener('keydown', function(e){
            if(e.key==='Backspace' && !this.value && i>0){ boxes[i-1].value=''; boxes[i-1].focus(); sync(); }
            if(e.key==='ArrowLeft' && i>0) boxes[i-1].focus();
            if(e.key==='ArrowRight' && i<5) boxes[i+1].focus();
        });
        box.addEventListener('paste', function(e){
            e.preventDefault();
            var p = (e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'');
            p.split('').slice(0,6).forEach(function(ch,j){ if(boxes[j]) boxes[j].value=ch; });
            boxes[Math.min(p.length,5)].focus();
            sync();
        });
    });

    boxes[0] && boxes[0].focus();

    window.doVerify = function(){
        document.getElementById('successOverlay').classList.add('show');
    };

    <?php if($errors->has('otp_code')): ?>
    grid.classList.add('shake');
    boxes.forEach(function(b){ b.value=''; b.classList.remove('filled','done'); });
    boxes[0].focus();
    <?php endif; ?>

    var secs = 600, el = document.getElementById('countdown');
    var tick = setInterval(function(){
        secs--;
        if(secs<=0){ clearInterval(tick); el.textContent='Expired'; el.classList.add('urgent'); return; }
        var m = Math.floor(secs/60).toString().padStart(2,'0');
        var s = (secs%60).toString().padStart(2,'0');
        el.textContent = m+':'+s;
        if(secs<=60) el.classList.add('urgent');
    },1000);
})();
</script>
</body>
</html><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/auth/otp-verify.blade.php ENDPATH**/ ?>