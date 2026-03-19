<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Face — <?php echo e(config('app.name')); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0; min-height: 100vh;
            background: radial-gradient(ellipse at 20% 50%, #1e1b4b 0%, #111827 60%);
            font-family: 'Segoe UI', sans-serif;
            display: flex; align-items: center; justify-content: center;
        }

        .card {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.12);
            backdrop-filter: blur(18px);
            border-radius: 20px;
            padding: 40px 36px;
            width: 420px;
            max-width: 95vw;
            color: #e2e8f0;
            box-shadow: 0 20px 60px rgba(0,0,0,.5);
        }

        .icon-wrap {
            text-align: center;
            margin-bottom: 24px;
        }
        .icon-wrap .icon {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 32px;
        }
        h1 { margin: 0 0 6px; font-size: 22px; font-weight: 700; text-align: center; }
        .subtitle { font-size: 13px; color: #94a3b8; text-align: center; margin-bottom: 28px; }

        .video-zone {
            position: relative;
            width: 280px; height: 210px;
            margin: 0 auto 20px;
            border-radius: 12px;
            overflow: hidden;
            background: #0f172a;
            border: 2px solid rgba(99,102,241,.4);
        }
        #video  { width:100%; height:100%; object-fit:cover; display:block; }
        #overlay { position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none; }

        #enrollStatus { font-size:13px; text-align:center; min-height:22px; margin-bottom:16px; color:#94a3b8; }
        .status-detecting { color: #3b82f6; }
        .status-ok  { color: #22c55e; font-weight: 600; }
        .status-fail { color: #ef4444; }

        .btn {
            width: 100%;
            padding: 13px;
            border: none; border-radius: 10px; cursor: pointer;
            font-size: 15px; font-weight: 700; margin-bottom: 10px;
            transition: opacity .2s;
        }
        .btn:disabled { opacity: .4; cursor: not-allowed; }
        .btn-start { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; }
        .btn-save  { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
        .btn-back  { background: rgba(255,255,255,.08); color: #94a3b8; border: 1px solid rgba(255,255,255,.12); }

        .info-note {
            background: rgba(99,102,241,.1);
            border: 1px solid rgba(99,102,241,.25);
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 12px;
            color: #a5b4fc;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .error-msg { background:#fef2f2; border:1px solid #fca5a5; border-radius:8px; padding:10px 14px; font-size:13px; color:#dc2626; margin-bottom:14px; }
        .success-msg { background:#f0fdf4; border:1px solid #86efac; border-radius:8px; padding:10px 14px; font-size:13px; color:#16a34a; margin-bottom:14px; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon-wrap">
        <div class="icon">🎭</div>
    </div>
    <h1>Enroll Your Face</h1>
    <p class="subtitle">Your face will be used for biometric login verification</p>

    <?php if($errors->any()): ?>
        <div class="error-msg"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="success-msg"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="info-note">
        📌 Stand in a well-lit area, face the camera directly, and stay still while scanning. Your face data is stored securely on our servers and never shared.
    </div>

    <div class="video-zone">
        <video id="video" autoplay muted playsinline></video>
        <canvas id="overlay"></canvas>
    </div>

    <div id="enrollStatus">Click "Start Camera" to begin</div>

    
    <div style="text-align:center;margin-bottom:12px;">
        <img id="facePreview"
             style="display:none;width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #22c55e;box-shadow:0 0 0 4px rgba(34,197,94,.25)"
             alt="Your face">
    </div>

    <button class="btn btn-start" id="startBtn" onclick="startEnroll()">
        📷 Start Camera
    </button>

    <form method="POST" action="<?php echo e(route('auth.face.enroll')); ?>" id="enrollForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="face_descriptor" id="faceDescriptor" value="">
        <input type="hidden" name="face_photo" id="facePhoto" value="">
        <button type="submit" class="btn btn-save" id="saveBtn" disabled>
            ✅ Save Face Profile
        </button>
    </form>

    <a href="<?php echo e(route('profile.show')); ?>" class="btn btn-back" style="display:block;text-align:center;text-decoration:none;">
        ← Back to Profile
    </a>
</div>

<script>
    const _FACE_WEIGHTS = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@0.22.2/weights';

    // Pre-load models silently in the background as soon as the page is ready
    let _modelsReady = false;
    let _modelsPromise = null;

    function _loadModels() {
        if (_modelsPromise) return _modelsPromise;
        if (typeof faceapi === 'undefined') return Promise.reject(new Error('face-api.js not loaded'));

        _modelsPromise = Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(_FACE_WEIGHTS),
            faceapi.nets.faceLandmark68TinyNet.loadFromUri(_FACE_WEIGHTS),
            faceapi.nets.faceRecognitionNet.loadFromUri(_FACE_WEIGHTS),
        ]).then(() => { _modelsReady = true; });
        return _modelsPromise;
    }

    // Start pre-loading immediately when page loads
    window.addEventListener('DOMContentLoaded', () => {
        const statusEl = document.getElementById('enrollStatus');
        statusEl.className = 'status-detecting';
        statusEl.textContent = '⏳ Loading face recognition models in background…';
        _loadModels()
            .then(() => {
                statusEl.className = '';
                statusEl.textContent = '✅ Models ready. Click "Start Camera" to begin.';
            })
            .catch(() => {
                statusEl.className = 'status-fail';
                statusEl.textContent = '⚠️ Could not pre-load models. Will retry when you click Start Camera.';
            });
    });

    async function startEnroll() {
        const statusEl = document.getElementById('enrollStatus');
        const video    = document.getElementById('video');
        const overlay  = document.getElementById('overlay');
        const startBtn = document.getElementById('startBtn');

        startBtn.disabled = true;

        // Check face-api.js is available
        if (typeof faceapi === 'undefined') {
            statusEl.className   = 'status-fail';
            statusEl.textContent = '❌ Face recognition library failed to load. Please refresh the page or check your internet connection.';
            startBtn.disabled = false;
            return;
        }

        // Start camera
        statusEl.className   = 'status-detecting';
        statusEl.textContent = '⏳ Starting camera…';
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
            statusEl.textContent = '❌ Cannot access camera. Please allow camera permission and try again.';
            startBtn.disabled = false;
            return;
        }

        // Load/await models
        if (!_modelsReady) {
            statusEl.textContent = '⏳ Loading face recognition models…';
            try {
                await _loadModels();
            } catch (e) {
                stream.getTracks().forEach(t => t.stop());
                statusEl.className   = 'status-fail';
                statusEl.textContent = '❌ Failed to load face models. Please check your internet connection and try again.';
                startBtn.disabled = false;
                return;
            }
        }

        statusEl.textContent = '🔍 Point your face at the camera…';
        _runEnrollLoop(video, overlay, stream, statusEl);
    }

    function _runEnrollLoop(video, overlay, stream, statusEl) {
        const ctx  = overlay.getContext('2d');
        const opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.45 });
        let hits = 0;
        let lastDescriptor = null;

        const loop = setInterval(async () => {
            if (video.readyState < 2) return;

            const result = await faceapi
                .detectSingleFace(video, opts)
                .withFaceLandmarks(true)
                .withFaceDescriptor();

            ctx.clearRect(0, 0, overlay.width, overlay.height);

            if (result) {
                hits++;
                lastDescriptor = result.descriptor;
                const { x, y, width, height } = result.detection.box;
                const pct   = Math.min(100, hits * 7);
                const color = hits >= 14 ? '#22c55e' : '#6366f1';

                ctx.strokeStyle = color;
                ctx.lineWidth   = 3;
                ctx.strokeRect(x, y, width, height);

                [[x,y,1,1],[x+width,y,-1,1],[x,y+height,1,-1],[x+width,y+height,-1,-1]]
                    .forEach(([cx,cy,dx,dy]) => {
                        ctx.beginPath();
                        ctx.moveTo(cx+dx*20, cy); ctx.lineTo(cx, cy); ctx.lineTo(cx, cy+dy*20);
                        ctx.stroke();
                    });

                ctx.fillStyle = color;
                ctx.font      = 'bold 11px sans-serif';
                ctx.fillText(`Face ${Math.round(result.detection.score * 100)}% ● ${pct}%`,
                             x + 2, Math.max(y - 4, 14));

                statusEl.className   = 'status-detecting';
                statusEl.textContent = `🔍 Scanning face… ${pct}%`;

                if (hits >= 14) {
                    clearInterval(loop);
                    ctx.clearRect(0, 0, overlay.width, overlay.height);

                    // Capture face snapshot from video frame (120×90 JPEG thumbnail)
                    const snap = document.createElement('canvas');
                    snap.width  = 120;
                    snap.height = 90;
                    snap.getContext('2d').drawImage(video, 0, 0, 120, 90);
                    const facePhoto = snap.toDataURL('image/jpeg', 0.82);

                    stream.getTracks().forEach(t => t.stop());

                    document.getElementById('faceDescriptor').value = JSON.stringify(Array.from(lastDescriptor));
                    document.getElementById('facePhoto').value      = facePhoto;
                    document.getElementById('saveBtn').disabled     = false;

                    // Show live preview of captured face
                    const preview = document.getElementById('facePreview');
                    preview.src   = facePhoto;
                    preview.style.display = 'block';

                    statusEl.className   = 'status-ok';
                    statusEl.textContent = '✅ Face captured! Click "Save Face Profile" to save.';
                }
            } else {
                hits = Math.max(0, hits - 1);
                statusEl.textContent = hits > 3
                    ? `🔍 Scanning face… ${Math.min(100, hits * 7)}%`
                    : '🔍 Point your face at the camera…';
            }
        }, 200);
    }
</script>
</body>
</html>
<?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/auth/face-enroll.blade.php ENDPATH**/ ?>