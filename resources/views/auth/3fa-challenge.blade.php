<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify your identity — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"
            onload="window._faceApiReady=true"
            onerror="window._faceApiReady=false"></script>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:flex-start;justify-content:center;padding:24px;background:#0a0a1a;position:relative;overflow-x:hidden;overflow-y:auto}
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 80% 60% at 20% 20%,rgba(99,102,241,.18) 0%,transparent 60%),radial-gradient(ellipse 60% 60% at 80% 80%,rgba(139,92,246,.15) 0%,transparent 60%);pointer-events:none}
.bg-blob{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;animation:floatBlob 8s ease-in-out infinite alternate}
.bg-blob-1{width:500px;height:500px;background:rgba(99,102,241,.07);top:-100px;left:-100px}
.bg-blob-2{width:400px;height:400px;background:rgba(139,92,246,.07);bottom:-100px;right:-100px;animation-delay:3s}
@keyframes floatBlob{0%{transform:translate(0,0) scale(1)}100%{transform:translate(30px,20px) scale(1.08)}}
.grid-pattern{position:fixed;inset:0;background-image:radial-gradient(rgba(255,255,255,.04) 1px,transparent 1px);background-size:32px 32px;pointer-events:none}

/* Card */
.card{position:relative;width:100%;max-width:460px;background:rgba(255,255,255,.03);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid rgba(255,255,255,.08);border-radius:24px;padding:32px 36px;box-shadow:0 32px 80px rgba(0,0,0,.5),inset 0 1px 0 rgba(255,255,255,.06);overflow:visible;margin:auto}
.card::before{content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);width:60%;height:1px;background:linear-gradient(90deg,transparent,rgba(99,102,241,.8),transparent)}

/* Header */
.card-header{text-align:center;margin-bottom:28px}
.shield-wrap{display:flex;align-items:center;justify-content:center;margin-bottom:16px}
.shield-icon{width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;box-shadow:0 12px 30px rgba(245,158,11,.4);position:relative}
.shield-icon::after{content:'';position:absolute;inset:-5px;border-radius:21px;border:1px solid rgba(245,158,11,.3);animation:pulse 2.5s ease-in-out infinite}
@keyframes pulse{0%,100%{opacity:.5;transform:scale(1)}50%{opacity:1;transform:scale(1.05)}}
.shield-icon i{font-size:22px;color:#fff}
.card-header h1{font-size:21px;font-weight:800;color:#fff;letter-spacing:-.4px;margin-bottom:6px}
.card-header p{font-size:13px;color:rgba(255,255,255,.45);line-height:1.55}





/* Error */
.alert-err{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);border-radius:10px;padding:11px 14px;font-size:13px;color:#fca5a5;margin-bottom:16px;display:flex;align-items:flex-start;gap:8px}
.alert-err i{color:#f87171;flex-shrink:0;margin-top:1px}

/* Method selector */
.method-selector{display:flex;gap:6px;margin-bottom:20px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:4px}
.method-btn{flex:1;padding:8px 6px;border:none;background:transparent;border-radius:9px;font-size:11px;font-weight:600;color:rgba(255,255,255,.35);cursor:pointer;transition:all .2s;font-family:'Inter',sans-serif;display:flex;flex-direction:column;align-items:center;gap:3px}
.method-btn i{font-size:14px}
.method-btn.active{background:rgba(99,102,241,.25);color:#c7d2fe;border:1px solid rgba(99,102,241,.35)}
.method-btn:hover:not(.active){background:rgba(255,255,255,.05);color:rgba(255,255,255,.6)}

/* Panels */
.panel{display:none}.panel.active{display:block}

/* Question */
.question-box{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:12px 14px;font-size:13px;color:rgba(255,255,255,.7);margin-bottom:16px;display:flex;align-items:flex-start;gap:9px;line-height:1.5}
.question-box i{color:#a5b4fc;flex-shrink:0;margin-top:1px}
label.field-label{display:block;font-size:11px;font-weight:700;color:rgba(255,255,255,.35);letter-spacing:1px;text-transform:uppercase;margin-bottom:8px}
.text-input{width:100%;padding:12px 14px;background:rgba(255,255,255,.04);border:1.5px solid rgba(255,255,255,.1);border-radius:10px;font-size:14px;color:#fff;font-family:'Inter',sans-serif;outline:none;transition:all .2s}
.text-input:focus{background:rgba(99,102,241,.1);border-color:rgba(99,102,241,.5);box-shadow:0 0 0 4px rgba(99,102,241,.12)}
.text-input::placeholder{color:rgba(255,255,255,.2)}
.field-error{font-size:12px;color:#f87171;margin-top:6px;display:flex;align-items:center;gap:5px}

/* Buttons */
.btn-primary{width:100%;padding:13px;border:none;border-radius:12px;font-size:14px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:9px;transition:all .3s;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;box-shadow:0 8px 28px rgba(99,102,241,.35);margin-top:16px}
.btn-primary:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 14px 36px rgba(99,102,241,.5);filter:brightness(1.08)}
.btn-primary:disabled{opacity:.35;cursor:not-allowed;transform:none;box-shadow:none}

/* Biometric */
.bio-zone{text-align:center;padding:8px 0 0}
#videoWrap{position:relative;display:inline-block;border-radius:12px;overflow:hidden;border:2px solid rgba(255,255,255,.1)}
#videoWrap canvas{position:absolute;top:0;left:0}
video{width:260px;height:195px;object-fit:cover;background:#0f172a;display:block}
#bioStatus{margin-top:10px;font-size:13px;font-weight:600}
.status-detecting{color:#fbbf24}
.status-ok{color:#4ade80}
.status-fail{color:#f87171}
.btn-start-bio{margin-top:12px;padding:10px 24px;background:rgba(99,102,241,.2);border:1.5px solid rgba(99,102,241,.35);border-radius:10px;color:#a5b4fc;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;transition:all .2s;display:inline-flex;align-items:center;gap:7px}
.btn-start-bio:hover{background:rgba(99,102,241,.3)}

/* Email */
.email-sent{background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.25);border-radius:10px;padding:12px 16px;font-size:13px;color:#86efac;margin-bottom:16px;display:flex;align-items:center;gap:9px}
.email-intro{text-align:center;padding:8px 0}
.email-intro .mail-icon{width:52px;height:52px;border-radius:14px;background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:22px;color:#a5b4fc}
.email-intro p{font-size:13px;color:rgba(255,255,255,.5);line-height:1.6}
.email-intro strong{color:rgba(255,255,255,.8)}

/* Bio no-face warning */
.bio-warn{background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:10px;padding:11px 14px;font-size:12px;color:#fcd34d;margin-bottom:14px;display:flex;align-items:flex-start;gap:8px;line-height:1.5}
.bio-warn a{color:#fbbf24;font-weight:700}
</style>
</head>
<body>
<div class="bg-blob bg-blob-1"></div>
<div class="bg-blob bg-blob-2"></div>
<div class="grid-pattern"></div>

<div class="card">

    {{-- Header --}}
    <div class="card-header">
        <div class="shield-wrap">
            <div class="shield-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
        </div>
        <h1>Verify it's you</h1>
        <p>We noticed something unusual with this sign-in.<br>For your security, please confirm your identity.</p>
    </div>



    {{-- Errors --}}
    @if($errors->any())
        <div class="alert-err">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>@foreach($errors->all() as $e){{ $e }}<br>@endforeach</div>
        </div>
    @endif

    {{-- Method tabs --}}
    <div class="method-selector">
        <button class="method-btn active" id="tab-question" onclick="switchTab('question')">
            <i class="fa-solid fa-key"></i> Security Q
        </button>
        <button class="method-btn" id="tab-biometric" onclick="switchTab('biometric')">
            <i class="fa-solid fa-face-viewfinder"></i> Face ID
        </button>
        <button class="method-btn" id="tab-email" onclick="switchTab('email')">
            <i class="fa-solid fa-envelope"></i> Email link
        </button>
    </div>

    {{-- Panel 1: Security Question --}}
    <div class="panel active" id="panel-question">
        <form method="POST" action="{{ route('auth.3fa.verify') }}">
            @csrf
            <input type="hidden" name="method" value="security_question">

            <div class="question-box">
                <i class="fa-solid fa-circle-question"></i>
                <span>{{ $user->security_question ?? 'What is your registered email address?' }}</span>
            </div>

            <label class="field-label">Your answer</label>
            <input class="text-input" type="text" name="security_answer"
                   placeholder="Type your answer…" autocomplete="off" autofocus>
            @error('security_answer')
                <div class="field-error"><i class="fa-solid fa-triangle-exclamation"></i>{{ $message }}</div>
            @enderror

            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-arrow-right"></i> Continue
            </button>
        </form>
    </div>

    {{-- Panel 2: Biometric --}}
    <div class="panel" id="panel-biometric">
        @error('biometric')
            <div class="alert-err" style="margin-bottom:14px">
                <i class="fa-solid fa-circle-exclamation"></i>{{ $message }}
                <div style="margin-top:8px;font-size:11.5px;opacity:.8">
                    Try better lighting, face the camera straight, or
                    <a href="{{ route('auth.face.enroll.form') }}" style="color:#fca5a5;text-decoration:underline">re-enroll your face</a>
                    after logging in via Security Question.
                </div>
            </div>
        @enderror

        @if(! $user->face_descriptor)
            <div class="bio-warn">
                <i class="fa-solid fa-triangle-exclamation" style="margin-top:1px"></i>
                <span>No face profile set up yet. Sign in with Security Question or Email link, then
                <a href="{{ route('auth.face.enroll.form') }}">enroll your face</a> in your profile.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('auth.3fa.verify') }}" id="bioForm">
            @csrf
            <input type="hidden" name="method" value="biometric">
            <input type="hidden" name="biometric_verified" id="bioVerified" value="">
            <input type="hidden" name="face_descriptor" id="faceDescriptor" value="">

            <div class="bio-zone">
                <div id="videoWrap">
                    <video id="video" autoplay muted playsinline></video>
                    <canvas id="overlay"></canvas>
                </div>
                <br>
                <button type="button" class="btn-start-bio" id="startBioBtn" onclick="startBiometric()">
                    <i class="fa-solid fa-camera"></i> Start face recognition
                </button>
                <div id="bioStatus"></div>
            </div>

            <button type="button" class="btn-primary" id="bioSubmit" disabled onclick="submitBioForm()">
                <i class="fa-solid fa-check-circle"></i> Confirm &amp; Sign in
            </button>
        </form>
    </div>

    {{-- Panel 3: Email --}}
    <div class="panel" id="panel-email">
        @if(session('email_confirm_sent'))
            <div class="email-sent">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('email_confirm_sent') }}
            </div>
        @endif

        <div class="email-intro">
            <div class="mail-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
            <p>We'll send a <strong>secure sign-in link</strong> to<br>
               <strong>{{ $user->email }}</strong><br>
               Click the link to complete sign-in. Valid for <strong>15 minutes</strong>.
            </p>
        </div>

        <form method="POST" action="{{ route('auth.3fa.email.send') }}">
            @csrf
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-paper-plane"></i> Send sign-in link
            </button>
        </form>

        <p style="font-size:12px;color:rgba(255,255,255,.25);text-align:center;margin-top:12px">
            Didn't get it? Check spam or use another method above.
        </p>
    </div>

</div>

<script>
    function switchTab(name) {
        document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        document.getElementById('panel-' + name).classList.add('active');
    }

    @if($errors->has('biometric'))
    window.addEventListener('load', () => switchTab('biometric'));
    @endif

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

    async function submitBioForm() {
        try {
            const res  = await fetch('/csrf-refresh');
            const data = await res.json();
            document.querySelector('#bioForm input[name="_token"]').value = data.token;
        } catch (e) {
            // proceed with existing token if refresh fails
        }
        document.getElementById('bioForm').submit();
    }

</script>
</body>
</html>
