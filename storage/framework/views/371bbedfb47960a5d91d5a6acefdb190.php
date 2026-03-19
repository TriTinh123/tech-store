<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>3FA Verification — <?php echo e(config('app.name')); ?></title>
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet"/>
    
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"
            onload="window._faceApiReady=true"
            onerror="window._faceApiReady=false"></script>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
        body{background:linear-gradient(135deg,#1e1b4b 0%,#312e81 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{width:100%;max-width:560px;background:#fff;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.4);overflow:hidden}
        .header{background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;padding:32px;text-align:center}
        .header .icon{font-size:52px;margin-bottom:12px}
        .header h1{font-size:22px;font-weight:700;margin-bottom:6px}
        .header p{font-size:13px;opacity:.85}
        .body{padding:32px}

        /* Step progress */
        .steps{display:flex;justify-content:center;gap:0;margin-bottom:28px;align-items:center}
        .step{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0}
        .step.done{background:#22c55e;color:#fff}
        .step.active{background:#ef4444;color:#fff;box-shadow:0 0 0 4px rgba(239,68,68,.25)}
        .step-line{flex:1;height:2px;background:#e2e8f0}
        .step-line.done{background:#22c55e}

        /* Risk box */
        .risk-box{border-radius:14px;padding:20px;margin-bottom:24px;border:2px solid}
        .risk-critical{background:#fff1f2;border-color:#ef4444}
        .risk-high{background:#fffbeb;border-color:#f59e0b}
        .risk-header{display:flex;align-items:center;gap:12px;margin-bottom:14px}
        .risk-badge{padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
        .badge-critical{background:#fee2e2;color:#dc2626}
        .badge-high{background:#fef3c7;color:#b45309}
        .risk-score-wrap{display:flex;align-items:baseline;gap:6px}
        .risk-score-num{font-size:36px;font-weight:800;color:#1e293b}
        .risk-score-sub{font-size:13px;color:#64748b}
        .risk-bar-wrap{margin:12px 0}
        .risk-bar-bg{height:10px;background:#e2e8f0;border-radius:5px;overflow:hidden}
        .risk-bar{height:100%;border-radius:5px;transition:width 1.2s ease}
        .risk-factors li{font-size:13px;color:#475569;margin-left:20px;margin-top:4px;line-height:1.6}

        /* Tabs */
        .tabs{display:flex;gap:8px;margin-bottom:20px}
        .tab{flex:1;padding:10px;border:2px solid #e2e8f0;background:#fff;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;color:#64748b;text-align:center}
        .tab.active{border-color:#3b82f6;background:#eff6ff;color:#2563eb}

        /* Form elements */
        .tab-panel{display:none}
        .tab-panel.active{display:block}
        .form-group{margin-bottom:18px}
        .form-group label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:8px}
        .form-group input{width:100%;padding:12px 15px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;transition:all .2s;background:#f8fafc}
        .form-group input:focus{outline:none;border-color:#3b82f6;background:#fff;box-shadow:0 0 0 4px rgba(59,130,246,.1)}
        .question-text{background:#f1f5f9;border-radius:8px;padding:12px 14px;font-size:14px;color:#374151;margin-bottom:16px;border-left:4px solid #3b82f6}
        .error-msg{color:#ef4444;font-size:12px;margin-top:6px}
        .alert-danger{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px}

        /* Biometric */
        .bio-zone{text-align:center;padding:20px 0}
        #videoWrap{position:relative;display:inline-block;border-radius:12px;overflow:hidden;border:3px solid #e2e8f0}
        #videoWrap canvas{position:absolute;top:0;left:0}
        video{width:280px;height:210px;object-fit:cover;background:#1e293b;display:block}
        .bio-btn{margin-top:14px;padding:12px 28px;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:all .2s}
        .bio-btn:hover{transform:translateY(-1px)}
        #bioStatus{margin-top:12px;font-size:13px;font-weight:600}
        .status-detecting{color:#f59e0b}
        .status-ok{color:#22c55e}
        .status-fail{color:#ef4444}

        .btn{width:100%;padding:14px;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;transition:all .3s;margin-top:8px}
        .btn-danger{background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff}
        .btn-danger:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(239,68,68,.4)}
        .recommendation{font-size:13px;color:#64748b;text-align:center;margin-top:12px}
    </style>
</head>
<body>
<div class="card">
    
    <div class="header">
        <div class="icon">🚨</div>
        <h1>Third Factor Verification (3FA)</h1>
        <p>AI detected unusual behavior — additional verification required</p>
    </div>

    <div class="body">
        
        <div class="steps">
            <div class="step done">✓</div>
            <div class="step-line done"></div>
            <div class="step done">✓</div>
            <div class="step-line done"></div>
            <div class="step done">AI</div>
            <div class="step-line done"></div>
            <div class="step active">3</div>
        </div>

        
        <?php
            $level   = $riskData['risk_level'] ?? 'high';
            $numeric = $riskData['risk_numeric'] ?? 75;
            $factors = $riskData['explanation'] ?? [];
            $rec     = $riskData['recommendation'] ?? '';
            $barColor = $level === 'critical' ? '#ef4444' : '#f59e0b';
        ?>

        <div class="risk-box risk-<?php echo e($level); ?>">
            <div class="risk-header">
                <div>
                    <span class="risk-badge badge-<?php echo e($level); ?>">
                        <?php echo e(strtoupper($level)); ?> RISK
                    </span>
                </div>
                <div class="risk-score-wrap">
                    <span class="risk-score-num"><?php echo e($numeric); ?></span>
                    <span class="risk-score-sub">/ 100</span>
                </div>
            </div>

            <div class="risk-bar-wrap">
                <div class="risk-bar-bg">
                    <div class="risk-bar" id="riskBar"
                         style="width:0;background:<?php echo e($barColor); ?>"></div>
                </div>
            </div>

            <?php if($factors): ?>
                <ul class="risk-factors">
                    <?php $__currentLoopData = $factors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>⚠ <?php echo e($f); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </div>

        
        <?php if($errors->any()): ?>
            <div class="alert-danger">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e($e); ?><br> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        
        <div class="tabs">
            <div class="tab active" onclick="switchTab('question')">🔑 Security question</div>
            <div class="tab" onclick="switchTab('biometric')">🎭 Biometric</div>
            <div class="tab" onclick="switchTab('email')">📧 Email Confirmation</div>
        </div>

        
        <div class="tab-panel active" id="panel-question">
            <form method="POST" action="<?php echo e(route('auth.3fa.verify')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="method" value="security_question">

                <?php if($user->security_question): ?>
                    <div class="question-text">
                        ❓ <?php echo e($user->security_question); ?>

                    </div>
                <?php else: ?>
                    <div class="question-text">
                        ❓ What is your registered email address?
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Your answer</label>
                    <input type="text" name="security_answer"
                           placeholder="Enter your answer…"
                           autocomplete="off" autofocus>
                    <?php $__errorArgs = ['security_answer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="error-msg"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit" class="btn btn-danger">
                    Confirm &amp; Sign In →
                </button>
            </form>
        </div>

        
        <div class="tab-panel" id="panel-biometric">
            <?php $__errorArgs = ['biometric'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="alert-danger" style="margin-bottom:14px">❌ <?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <?php if(! $user->face_descriptor): ?>
                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px 14px;font-size:12px;color:#92400e;margin-bottom:14px;">
                    ⚠️ <strong>No face profile enrolled.</strong>
                    Biometric will not verify your identity until you
                    <a href="<?php echo e(route('auth.face.enroll.form')); ?>" style="color:#b45309;font-weight:700;">enroll your face</a>
                    after logging in with another method.
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('auth.3fa.verify')); ?>" id="bioForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="method" value="biometric">
                <input type="hidden" name="biometric_verified" id="bioVerified" value="">
                <input type="hidden" name="face_descriptor" id="faceDescriptor" value="">

                <div class="bio-zone">
                    <div id="videoWrap">
                        <video id="video" autoplay muted playsinline></video>
                        <canvas id="overlay"></canvas>
                    </div>
                    <button type="button" class="bio-btn" id="startBioBtn" onclick="startBiometric()">
                        📷 Start face recognition
                    </button>
                    <div id="bioStatus"></div>
                </div>

                <button type="submit" class="btn btn-danger" id="bioSubmit" disabled style="opacity:.4">
                    ✅ Confirm biometric
                </button>
            </form>
        </div>

        
        <div class="tab-panel" id="panel-email">
            <?php if(session('email_confirm_sent')): ?>
                <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:16px 20px;margin-bottom:16px;font-size:13px;color:#166534;">
                    ✅ <?php echo e(session('email_confirm_sent')); ?>

                </div>
            <?php endif; ?>

            <div style="text-align:center;padding:16px 0 10px;">
                <div style="font-size:48px;margin-bottom:12px;">📬</div>
                <p style="font-size:14px;color:#475569;line-height:1.7;margin-bottom:20px;">
                    The system will send a <strong>security confirmation link</strong> to the email
                    <strong><?php echo e($user->email); ?></strong>.<br>
                    Click the link in the email to complete sign-in. Valid for <strong>15 minutes</strong>.
                </p>
            </div>

            <form method="POST" action="<?php echo e(route('auth.3fa.email.send')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-danger">
                    📧 Send confirmation email now
                </button>
            </form>

            <p style="font-size:12px;color:#94a3b8;text-align:center;margin-top:12px;">
                Didn't receive the email? Check your spam folder or choose another method.
            </p>
        </div>

        <p class="recommendation"><?php echo e($rec); ?></p>
    </div>
</div>

<script>
    // ── Risk bar animation ────────────────────────────────────────────────────
    window.addEventListener('load', () => {
        setTimeout(() => {
            document.getElementById('riskBar').style.width = '<?php echo e($numeric); ?>%';
        }, 300);
    });

    // ── Tab switching ─────────────────────────────────────────────────────────
    function switchTab(name, el) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        (el || event.target).classList.add('active');
        document.getElementById('panel-' + name).classList.add('active');
    }

    // Auto-open biometric tab if there is a biometric error
    <?php if($errors->has('biometric')): ?>
    window.addEventListener('load', () => {
        const bioTab = document.querySelector('.tab:nth-child(2)');
        if (bioTab) switchTab('biometric', bioTab);
    });
    <?php endif; ?>

    // ── Biometric: real face-api.js detection with canvas-animation fallback ────
    const _FACE_WEIGHTS = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js/weights';

    async function startBiometric() {
        const statusEl = document.getElementById('bioStatus');
        const video    = document.getElementById('video');
        const overlay  = document.getElementById('overlay');
        const btn      = document.getElementById('startBioBtn');

        btn.disabled = true;
        statusEl.className = 'status-detecting';
        statusEl.textContent = '⏳ Starting camera…';

        // 1. Request webcam access
        let stream;
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { width: 280, height: 210, facingMode: 'user' }
            });
            video.srcObject = stream;
            await video.play();
            overlay.width  = video.videoWidth  || 280;
            overlay.height = video.videoHeight || 210;
        } catch (e) {
            statusEl.className   = 'status-fail';
            statusEl.textContent = '❌ Cannot access camera. Please choose another method.';
            btn.disabled = false;
            return;
        }

        // 2. Require real face-api.js — no simulation fallback allowed
        if (!window._faceApiReady || typeof faceapi === 'undefined') {
            stream.getTracks().forEach(t => t.stop());
            statusEl.className   = 'status-fail';
            statusEl.textContent = '❌ Face recognition AI failed to load. Please use Security Question or Email Confirmation instead.';
            btn.disabled = false;
            return;
        }

        try {
            statusEl.textContent = '⏳ Loading face recognition AI model…';
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(_FACE_WEIGHTS),
                faceapi.nets.faceLandmark68TinyNet.loadFromUri(_FACE_WEIGHTS),
                faceapi.nets.faceRecognitionNet.loadFromUri(_FACE_WEIGHTS),
            ]);
            statusEl.textContent = '🔍 Point your face at the camera…';
            _runRealDetection(video, overlay, stream, statusEl);
        } catch (e) {
            stream.getTracks().forEach(t => t.stop());
            statusEl.className   = 'status-fail';
            statusEl.textContent = '❌ Failed to load face recognition models. Please use another verification method.';
            btn.disabled = false;
        }
    }

    // ── Real face detection loop (face-api.js TinyFaceDetector + descriptor) ─────────────────
    function _runRealDetection(video, overlay, stream, statusEl) {
        const ctx  = overlay.getContext('2d');
        const opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.45 });
        let hits = 0;

        const loop = setInterval(async () => {
            if (video.readyState < 2) return;

            // Use withFaceLandmarks + withFaceDescriptor when models are loaded
            let result = null;
            try {
                result = await faceapi
                    .detectSingleFace(video, opts)
                    .withFaceLandmarks(true)
                    .withFaceDescriptor();
            } catch (e) {
                // Fallback to detection only if descriptor models failed to load
                const det = await faceapi.detectSingleFace(video, opts);
                if (det) result = { detection: det, descriptor: null };
            }

            ctx.clearRect(0, 0, overlay.width, overlay.height);

            if (result) {
                hits++;
                const det   = result.detection || result;
                const { x, y, width, height } = det.box;
                const pct   = Math.min(100, hits * 9);
                const color = hits >= 11 ? '#22c55e' : '#3b82f6';

                // Bounding box
                ctx.strokeStyle = color;
                ctx.lineWidth   = 3;
                ctx.strokeRect(x, y, width, height);

                // Corner accent brackets
                [[x,y,1,1],[x+width,y,-1,1],[x,y+height,1,-1],[x+width,y+height,-1,-1]]
                    .forEach(([cx,cy,dx,dy]) => {
                        ctx.beginPath();
                        ctx.moveTo(cx+dx*20, cy); ctx.lineTo(cx, cy); ctx.lineTo(cx, cy+dy*20);
                        ctx.stroke();
                    });

                // Confidence label
                ctx.fillStyle = color;
                ctx.font      = 'bold 11px sans-serif';
                const score   = det.score ?? det.detection?.score ?? 0;
                ctx.fillText(`Face ${Math.round(score * 100)}% ● ${pct}%`,
                             x + 2, Math.max(y - 4, 14));

                statusEl.className   = 'status-detecting';
                statusEl.textContent = `🔍 Verifying… ${pct}%`;

                if (hits >= 11) {
                    clearInterval(loop);
                    stream.getTracks().forEach(t => t.stop());
                    ctx.clearRect(0, 0, overlay.width, overlay.height);

                    // Require a real 128-float descriptor — reject if none captured
                    if (!result.descriptor) {
                        statusEl.className   = 'status-fail';
                        statusEl.textContent = '❌ Could not capture face data. Try better lighting or use another method.';
                        document.getElementById('startBioBtn').disabled = false;
                        return;
                    }

                    document.getElementById('faceDescriptor').value =
                        JSON.stringify(Array.from(result.descriptor));
                    document.getElementById('bioVerified').value       = '1';
                    document.getElementById('bioSubmit').disabled      = false;
                    document.getElementById('bioSubmit').style.opacity = '1';
                    statusEl.className   = 'status-ok';
                    statusEl.textContent = '✅ Face captured! Click confirm to sign in.';
                }
            } else {
                hits = Math.max(0, hits - 1);
                statusEl.className   = 'status-detecting';
                statusEl.textContent = hits > 3
                    ? `🔍 Verifying… ${Math.min(100, hits * 9)}%`
                    : '🔍 Point your face at the camera frame…';
            }
        }, 200);
    }


</script>
</body>
</html>
<?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/auth/3fa-challenge.blade.php ENDPATH**/ ?>