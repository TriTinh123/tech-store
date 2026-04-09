<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In &mdash; {{ config('app.name', 'TechStore') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;background:#f0f4ff}
        .auth-shell{display:flex;min-height:100vh;width:100%}
        /* Left */
        .auth-left{width:42%;flex-shrink:0;background:linear-gradient(145deg,#0f172a 0%,#1e1b4b 40%,#312e81 100%);display:flex;flex-direction:column;justify-content:center;align-items:center;padding:60px 52px;position:relative;overflow:hidden;}
        .auth-left::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 20% 20%,rgba(99,102,241,.35) 0%,transparent 55%),radial-gradient(ellipse at 80% 80%,rgba(59,130,246,.25) 0%,transparent 55%);}
        .blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.18;animation:float 8s ease-in-out infinite;}
        .blob1{width:280px;height:280px;background:#6366f1;top:-80px;left:-80px;animation-delay:0s}
        .blob2{width:220px;height:220px;background:#3b82f6;bottom:-60px;right:-60px;animation-delay:3s}
        .blob3{width:160px;height:160px;background:#8b5cf6;top:50%;left:50%;transform:translate(-50%,-50%);animation-delay:1.5s}
        @keyframes float{0%,100%{transform:translateY(0) scale(1)}50%{transform:translateY(-18px) scale(1.05)}}
        .blob1,.blob2{animation-name:floatB}
        @keyframes floatB{0%,100%{transform:translateY(0)}50%{transform:translateY(-18px)}}
        .left-content{position:relative;z-index:1;width:100%;max-width:340px}
        .auth-brand{display:flex;align-items:center;gap:12px;margin-bottom:52px}
        .auth-brand-icon{width:44px;height:44px;background:linear-gradient(135deg,#6366f1,#3b82f6);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 8px 24px rgba(99,102,241,.45)}
        .auth-brand-name{font-size:22px;font-weight:800;color:#fff;letter-spacing:-.5px}
        .auth-tagline{font-size:28px;font-weight:800;color:#fff;line-height:1.25;margin-bottom:14px;letter-spacing:-.5px}
        .auth-tagline span{color:#a5b4fc}
        .auth-sub{font-size:14px;color:rgba(255,255,255,.5);line-height:1.7;margin-bottom:44px}
        .auth-features{display:flex;flex-direction:column;gap:16px}
        .auth-feat{display:flex;align-items:center;gap:14px}
        .auth-feat-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
        .auth-feat-text{font-size:13px;font-weight:500;color:rgba(255,255,255,.75)}
        /* Right */
        .auth-right{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 32px;overflow-y:auto;background:#fff;}
        .auth-form-wrap{width:100%;max-width:420px}
        .form-head{margin-bottom:32px}
        .form-head h2{font-size:26px;font-weight:800;color:#0f172a;letter-spacing:-.5px;margin-bottom:6px}
        .form-head p{font-size:14px;color:#64748b}
        /* Alerts */
        .a-success{background:#ecfdf5;color:#065f46;border:1px solid #6ee7b7;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:8px}
        .a-error{background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:20px}
        .a-error ul{margin:6px 0 0 0;padding-left:18px}
        /* Fields */
        .field{margin-bottom:18px}
        .field label{display:block;font-size:12px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.6px;margin-bottom:7px}
        .input-wrap{position:relative}
        .input-wrap i.fi{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:14px;pointer-events:none}
        .input-wrap input,.input-wrap select{width:100%;padding:12px 14px 12px 40px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-family:'Inter',sans-serif;color:#0f172a;background:#f8fafc;transition:border-color .2s,box-shadow .2s,background .2s;outline:none;}
        .input-wrap input:focus,.input-wrap select:focus{border-color:#6366f1;background:#fff;box-shadow:0 0 0 4px rgba(99,102,241,.12);}
        .input-wrap input::placeholder{color:#b0bac9;font-size:13.5px}
        .eye-btn{position:absolute;right:13px;top:50%;transform:translateY(-50%);cursor:pointer;color:#94a3b8;font-size:14px;transition:color .15s;background:none;border:none;padding:2px}
        .eye-btn:hover{color:#6366f1}
        .field-err{font-size:11.5px;color:#ef4444;margin-top:5px;display:flex;align-items:center;gap:4px}
        /* Row */
        .rf-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;font-size:13px}
        .rf-row label{display:flex;align-items:center;gap:7px;color:#475569;cursor:pointer}
        .rf-row input[type=checkbox]{width:15px;height:15px;accent-color:#6366f1;cursor:pointer}
        .rf-row a{color:#6366f1;font-weight:600;text-decoration:none}
        .rf-row a:hover{text-decoration:underline}
        /* Buttons */
        .btn-primary{width:100%;padding:13px;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;transition:all .2s;box-shadow:0 4px 14px rgba(99,102,241,.35);display:flex;align-items:center;justify-content:center;gap:8px;}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(99,102,241,.45)}
        .btn-primary:active{transform:translateY(0)}
        .divider{display:flex;align-items:center;gap:12px;margin:20px 0;font-size:12px;color:#94a3b8}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:#e2e8f0}
        .btn-google{width:100%;padding:12px 16px;border:1.5px solid #e2e8f0;background:#fff;border-radius:10px;font-size:14px;font-weight:600;color:#374151;font-family:'Inter',sans-serif;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:10px;}
        .btn-google:hover{background:#f8fafc;border-color:#c7d2fe;box-shadow:0 2px 10px rgba(0,0,0,.06)}
        .switch-link{text-align:center;font-size:13.5px;color:#64748b;margin-top:20px}
        .switch-link a{color:#6366f1;font-weight:700;text-decoration:none}
        .switch-link a:hover{text-decoration:underline}
        .copy-note{text-align:center;font-size:11px;color:#cbd5e1;margin-top:28px;line-height:1.6}
        @media(max-width:768px){.auth-left{display:none}.auth-right{padding:32px 20px}}
        /* Demo Panel */
        .demo-toggle{width:100%;margin-top:14px;padding:10px 14px;background:#fefce8;border:1.5px dashed #fbbf24;border-radius:10px;font-size:12.5px;font-weight:600;color:#92400e;cursor:pointer;display:flex;align-items:center;justify-content:space-between;transition:all .2s;font-family:'Inter',sans-serif;}
        .demo-toggle:hover{background:#fef9c3;border-color:#f59e0b}
        .demo-toggle .arrow{transition:transform .25s}
        .demo-toggle.open .arrow{transform:rotate(180deg)}
        .demo-panel{background:#fffbeb;border:1.5px solid #fde68a;border-top:none;border-radius:0 0 10px 10px;padding:16px;margin-top:-6px;display:none;}
        .demo-panel.open{display:block}
        .demo-panel-title{font-size:11px;font-weight:700;color:#b45309;text-transform:uppercase;letter-spacing:.6px;margin-bottom:12px;display:flex;align-items:center;gap:6px}
        .demo-options{display:flex;flex-direction:column;gap:8px;margin-bottom:14px}
        .demo-option{display:flex;align-items:flex-start;gap:10px;padding:8px 10px;border-radius:8px;background:#fff;border:1px solid #fde68a;cursor:pointer;transition:background .15s}
        .demo-option:hover{background:#fef9c3}
        .demo-option input[type=checkbox]{width:15px;height:15px;accent-color:#f59e0b;cursor:pointer;flex-shrink:0;margin-top:1px}
        .demo-option-info{flex:1}
        .demo-option-label{font-size:12.5px;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px}
        .demo-option-desc{font-size:11px;color:#9ca3af;margin-top:2px}
        .demo-badge{font-size:10px;padding:2px 7px;border-radius:20px;font-weight:700}
        .badge-red{background:#fee2e2;color:#dc2626}
        .badge-yellow{background:#fef9c3;color:#b45309}
        .badge-blue{background:#dbeafe;color:#1d4ed8}
        .demo-fail-wrap{margin-bottom:14px}
        .demo-fail-label{font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;display:flex;justify-content:space-between}
        .demo-fail-label span{color:#ef4444;font-weight:700}
        .demo-fail-wrap input[type=range]{width:100%;accent-color:#ef4444}
        .demo-ticks{display:flex;justify-content:space-between;font-size:10px;color:#9ca3af;margin-top:3px}
        .demo-prediction{padding:10px 12px;border-radius:8px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:8px;}
        .pred-normal{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0}
        .pred-otp{background:#fff7ed;color:#c2410c;border:1px solid #fed7aa}
        .pred-attack{background:#fef2f2;color:#991b1b;border:1px solid #fca5a5}
    </style>
</head>
<body>
<div class="auth-shell">
    <div class="auth-left">
        <div class="blob blob1"></div>
        <div class="blob blob2"></div>
        <div class="blob blob3"></div>
        <div class="left-content">
            <div class="auth-brand">
                <div class="auth-brand-icon"><i class="fas fa-bolt" style="color:#fff"></i></div>
                <span class="auth-brand-name">TechStore</span>
            </div>
            <h1 class="auth-tagline">Welcome<br>back to <span>TechStore</span></h1>
            <p class="auth-sub">Sign in to access your orders, wishlist, and exclusive member offers.</p>
            <div class="auth-features">
                <div class="auth-feat">
                    <div class="auth-feat-icon" style="background:rgba(16,185,129,.2)"><i class="fas fa-shield-alt" style="color:#34d399"></i></div>
                    <span class="auth-feat-text">AI-powered 3FA security on every login</span>
                </div>
                <div class="auth-feat">
                    <div class="auth-feat-icon" style="background:rgba(59,130,246,.2)"><i class="fas fa-shipping-fast" style="color:#60a5fa"></i></div>
                    <span class="auth-feat-text">Fast delivery &mdash; track orders in real time</span>
                </div>
                <div class="auth-feat">
                    <div class="auth-feat-icon" style="background:rgba(245,158,11,.2)"><i class="fas fa-star" style="color:#fbbf24"></i></div>
                    <span class="auth-feat-text">100% genuine products with warranty</span>
                </div>
                <div class="auth-feat">
                    <div class="auth-feat-icon" style="background:rgba(236,72,153,.2)"><i class="fas fa-headset" style="color:#f472b6"></i></div>
                    <span class="auth-feat-text">24/7 customer support</span>
                </div>
            </div>
        </div>
    </div>
    <div class="auth-right">
        <div class="auth-form-wrap">
            <div class="form-head">
                <h2>Sign In</h2>
                <p>Enter your credentials to continue</p>
            </div>
            @if(session('success'))
                <div class="a-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="a-error">
                    <strong><i class="fas fa-exclamation-circle"></i> Please fix the following:</strong>
                    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <form class="login-form" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope fi"></i>
                        <input type="email" id="email" name="email" placeholder="your@email.com" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    @error('email')<div class="field-err"><i class="fas fa-circle-exclamation"></i>{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock fi"></i>
                        <input type="password" id="password" name="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required autocomplete="current-password">
                        <button type="button" class="eye-btn" onclick="togglePassword('password',this)" tabindex="-1"><i class="fas fa-eye"></i></button>
                    </div>
                    @error('password')<div class="field-err"><i class="fas fa-circle-exclamation"></i>{{ $message }}</div>@enderror
                </div>
                <div class="rf-row">
                    <label><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember me</label>
                    <a href="{{ route('password.forgot') }}">Forgot password?</a>
                </div>
                <button type="submit" class="btn-primary"><i class="fas fa-sign-in-alt"></i> Sign In</button>
                <div class="divider">or</div>

                {{-- ══ DEMO MODE PANEL ══ --}}
                <input type="hidden" name="demo_mode" id="demo_mode" value="0">
                <input type="hidden" name="demo_failed_attempts" id="demo_failed_attempts" value="0">
                <input type="hidden" name="demo_new_ip" id="demo_new_ip" value="0">
                <input type="hidden" name="demo_new_device" id="demo_new_device" value="0">
                <input type="hidden" name="demo_geo_changed" id="demo_geo_changed" value="0">
                <input type="hidden" name="demo_ip_count" id="demo_ip_count" value="0">

                <button type="button" class="demo-toggle" id="demoToggleBtn" onclick="toggleDemo()">
                    <span>🎭 Demo Mode — Giả lập tấn công / bất thường</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </button>
                <div class="demo-panel" id="demoPanel">
                    <div class="demo-panel-title"><i class="fas fa-flask"></i> Chọn tín hiệu bất thường để giả lập</div>

                    <div class="demo-options">
                        <label class="demo-option" onclick="updateDemoPred()">
                            <input type="checkbox" id="chk_new_ip" onchange="syncDemo(); updateDemoPred()">
                            <div class="demo-option-info">
                                <div class="demo-option-label"><i class="fas fa-map-marker-alt" style="color:#3b82f6"></i> IP lạ (chưa từng đăng nhập) <span class="demo-badge badge-blue">IP</span></div>
                                <div class="demo-option-desc">Giả lập đăng nhập từ địa chỉ IP chưa biết</div>
                            </div>
                        </label>
                        <label class="demo-option" onclick="updateDemoPred()">
                            <input type="checkbox" id="chk_new_device" onchange="syncDemo(); updateDemoPred()">
                            <div class="demo-option-info">
                                <div class="demo-option-label"><i class="fas fa-laptop" style="color:#8b5cf6"></i> Thiết bị lạ <span class="demo-badge badge-blue">Device</span></div>
                                <div class="demo-option-desc">Giả lập trình duyệt / thiết bị chưa từng dùng</div>
                            </div>
                        </label>
                        <label class="demo-option" onclick="updateDemoPred()">
                            <input type="checkbox" id="chk_geo" onchange="syncDemo(); updateDemoPred()">
                            <div class="demo-option-info">
                                <div class="demo-option-label"><i class="fas fa-globe" style="color:#10b981"></i> Vị trí địa lý thay đổi <span class="demo-badge badge-yellow">Geo</span></div>
                                <div class="demo-option-desc">Giả lập đăng nhập từ quốc gia khác</div>
                            </div>
                        </label>
                        <label class="demo-option" onclick="updateDemoPred()">
                            <input type="checkbox" id="chk_multi_ip" onchange="syncDemo(); updateDemoPred()">
                            <div class="demo-option-info">
                                <div class="demo-option-label"><i class="fas fa-network-wired" style="color:#f59e0b"></i> Nhiều IP trong 10 phút <span class="demo-badge badge-yellow">&gt;2 IPs</span></div>
                                <div class="demo-option-desc">Giả lập dùng 3+ địa chỉ IP liên tục</div>
                            </div>
                        </label>
                    </div>

                    <div class="demo-fail-wrap">
                        <div class="demo-fail-label">
                            Số lần sai mật khẩu giả lập:
                            <span id="fail_val_label">0 lần</span>
                        </div>
                        <input type="range" id="demo_fail_slider" min="0" max="12" step="1" value="0"
                               oninput="syncFailSlider(this.value); updateDemoPred()">
                        <div class="demo-ticks">
                            <span>0</span><span>3 (OTP)</span><span>6</span><span>10 (Lock)</span><span>12</span>
                        </div>
                    </div>

                    <div class="demo-prediction pred-normal" id="demoPred">
                        <i class="fas fa-check-circle"></i>
                        <span id="demoPredText">Bình thường — AI sẽ cho đăng nhập thẳng</span>
                    </div>
                </div>
                {{-- ══ END DEMO ══ --}}
                <button type="button" class="btn-google" onclick="loginWithGoogle()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC04"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Continue with Google
                </button>
            </form>
            <p class="switch-link">Don't have an account? <a href="{{ route('register') }}">Create one free</a></p>
            <p class="copy-note">&copy; 2026 TechStore. Secured by AI-based 3FA anomaly detection.</p>
        </div>
    </div>
</div>
<script>
    function togglePassword(id,btn){const i=document.getElementById(id),ic=btn.querySelector('i');i.type=i.type==='password'?(ic.classList.replace('fa-eye','fa-eye-slash'),'text'):(ic.classList.replace('fa-eye-slash','fa-eye'),'password');}
    function loginWithGoogle(){window.location.href="{{ route('auth.google') }}";}
    function setHiddenField(form,name,value){let el=form.querySelector('input[name="'+name+'"]');if(!el){el=document.createElement('input');el.type='hidden';el.name=name;form.appendChild(el);}el.value=value;}
    (function(){let lx=null,ly=null,lt=null;const sp=[];
        document.addEventListener('mousemove',function(e){const n=performance.now();if(lx!==null){const dx=e.clientX-lx,dy=e.clientY-ly,dt=n-lt;if(dt>0&&dt<500)sp.push(Math.sqrt(dx*dx+dy*dy)/dt);}lx=e.clientX;ly=e.clientY;lt=n;});
        const f=document.querySelector('form.login-form');f.addEventListener('submit',function(){const av=sp.length?sp.reduce((a,b)=>a+b,0)/sp.length:0;setHiddenField(f,'mouse_move_count',sp.length);setHiddenField(f,'mouse_avg_speed',parseFloat(av.toFixed(3)));});
    })();
    (function(){const pw=document.getElementById('password');const iv=[];let lk=null;
        pw.addEventListener('keydown',function(){const n=performance.now();if(lk!==null){const d=n-lk;if(d>0&&d<3000)iv.push(d);}lk=n;});
        const f=document.querySelector('form.login-form');f.addEventListener('submit',function(){const n=iv.length;let sp=150,ir=30;if(n>=2){sp=iv.reduce((a,b)=>a+b,0)/n;const v=iv.reduce((s,x)=>s+Math.pow(x-sp,2),0)/n;ir=Math.sqrt(v);}setHiddenField(f,'keystroke_speed_ms',Math.round(sp));setHiddenField(f,'keystroke_irregularity',Math.round(ir));setHiddenField(f,'screen_w',screen.width);setHiddenField(f,'screen_h',screen.height);setHiddenField(f,'timezone',Intl.DateTimeFormat().resolvedOptions().timeZone);});
    })();
    (function(){let cl=0;const t0=performance.now();document.addEventListener('click',function(){cl++;});const f=document.querySelector('form.login-form');f.addEventListener('submit',function(){const m=Math.max((performance.now()-t0)/60000,1/60);setHiddenField(f,'click_count_per_min',Math.round(cl/m));});})();
    // ── Demo Mode ─────────────────────────────────────────────────────────
    function toggleDemo(){
        const btn=document.getElementById('demoToggleBtn');
        const panel=document.getElementById('demoPanel');
        btn.classList.toggle('open');
        panel.classList.toggle('open');
        const on=panel.classList.contains('open');
        document.getElementById('demo_mode').value=on?'1':'0';
        if(on) updateDemoPred();
    }
    function syncDemo(){
        document.getElementById('demo_mode').value='1';
        document.getElementById('demo_new_ip').value=document.getElementById('chk_new_ip').checked?'1':'0';
        document.getElementById('demo_new_device').value=document.getElementById('chk_new_device').checked?'1':'0';
        document.getElementById('demo_geo_changed').value=document.getElementById('chk_geo').checked?'1':'0';
        document.getElementById('demo_ip_count').value=document.getElementById('chk_multi_ip').checked?'3':'0';
    }
    function syncFailSlider(v){
        document.getElementById('demo_failed_attempts').value=v;
        document.getElementById('fail_val_label').textContent=v+' lần';
    }
    function updateDemoPred(){
        const fail=parseInt(document.getElementById('demo_fail_slider').value)||0;
        const newIp=document.getElementById('chk_new_ip').checked;
        const newDev=document.getElementById('chk_new_device').checked;
        const geo=document.getElementById('chk_geo').checked;
        const multiIp=document.getElementById('chk_multi_ip').checked;
        const pred=document.getElementById('demoPred');
        const txt=document.getElementById('demoPredText');
        let level='normal';
        if(fail>=10||multiIp) level='attack';
        else if(fail>=3||(newIp&&newDev)||geo) level='otp';
        pred.className='demo-prediction';
        if(level==='attack'){
            pred.classList.add('pred-attack');
            txt.innerHTML='<strong>🚨 TẤN CÔNG</strong> — AI sẽ khóa tài khoản ngay';
        } else if(level==='otp'){
            pred.classList.add('pred-otp');
            txt.innerHTML='<strong>⚠️ Nghi ngờ</strong> — Hệ thống sẽ yêu cầu OTP (F2)';
        } else {
            pred.classList.add('pred-normal');
            txt.innerHTML='<strong>✅ Bình thường</strong> — AI sẽ cho đăng nhập thẳng';
        }
    }
</script>
</body>
</html>
