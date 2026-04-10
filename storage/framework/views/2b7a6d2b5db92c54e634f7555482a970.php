<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account &mdash; <?php echo e(config("app.name", "TechStore")); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:"Inter",sans-serif;min-height:100vh;display:flex;background:#f0f4ff}
        .auth-shell{display:flex;min-height:100vh;width:100%}
        .auth-left{width:40%;flex-shrink:0;background:linear-gradient(145deg,#0f172a 0%,#1e1b4b 40%,#312e81 100%);display:flex;flex-direction:column;justify-content:center;align-items:center;padding:60px 48px;position:relative;overflow:hidden;}
        .auth-left::before{content:"";position:absolute;inset:0;background:radial-gradient(ellipse at 20% 20%,rgba(99,102,241,.35) 0%,transparent 55%),radial-gradient(ellipse at 80% 80%,rgba(59,130,246,.25) 0%,transparent 55%);}
        .blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.18;}
        .blob1{width:280px;height:280px;background:#6366f1;top:-80px;left:-80px;animation:floatB 9s ease-in-out infinite;}
        .blob2{width:220px;height:220px;background:#3b82f6;bottom:-60px;right:-60px;animation:floatB 9s ease-in-out 3s infinite;}
        .blob3{width:160px;height:160px;background:#8b5cf6;top:50%;left:50%;transform:translate(-50%,-50%);animation:floatB 9s ease-in-out 1.5s infinite;}
        @keyframes floatB{0%,100%{transform:translateY(0)}50%{transform:translateY(-16px)}}
        .blob3{transform:translate(-50%,-50%)}
        .left-content{position:relative;z-index:1;width:100%;max-width:320px}
        .auth-brand{display:flex;align-items:center;gap:12px;margin-bottom:44px}
        .auth-brand-icon{width:44px;height:44px;background:linear-gradient(135deg,#6366f1,#3b82f6);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 8px 24px rgba(99,102,241,.45)}
        .auth-brand-name{font-size:22px;font-weight:800;color:#fff;letter-spacing:-.5px}
        .auth-tagline{font-size:26px;font-weight:800;color:#fff;line-height:1.25;margin-bottom:12px;letter-spacing:-.5px}
        .auth-tagline span{color:#a5b4fc}
        .auth-sub{font-size:13.5px;color:rgba(255,255,255,.5);line-height:1.7;margin-bottom:36px}
        .auth-features{display:flex;flex-direction:column;gap:14px}
        .auth-feat{display:flex;align-items:center;gap:14px}
        .auth-feat-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
        .auth-feat-text{font-size:13px;font-weight:500;color:rgba(255,255,255,.75)}
        .auth-right{flex:1;display:flex;align-items:flex-start;justify-content:center;padding:40px 32px;overflow-y:auto;background:#fff;}
        .auth-form-wrap{width:100%;max-width:460px;padding:8px 0}
        .form-head{margin-bottom:28px}
        .form-head h2{font-size:26px;font-weight:800;color:#0f172a;letter-spacing:-.5px;margin-bottom:6px}
        .form-head p{font-size:14px;color:#64748b}
        .a-success{background:#ecfdf5;color:#065f46;border:1px solid #6ee7b7;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:8px}
        .a-error{background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:20px}
        .a-error ul{margin:6px 0 0 0;padding-left:18px}
        .field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        .field{margin-bottom:16px}
        .field label{display:block;font-size:11.5px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.6px;margin-bottom:7px}
        .field label span{color:#ef4444}
        .input-wrap{position:relative}
        .input-wrap i.fi{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;pointer-events:none}
        .input-wrap input,.input-wrap select{width:100%;padding:11px 13px 11px 38px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-family:"Inter",sans-serif;color:#0f172a;background:#f8fafc;transition:border-color .2s,box-shadow .2s,background .2s;outline:none;}
        .input-wrap input:focus,.input-wrap select:focus{border-color:#6366f1;background:#fff;box-shadow:0 0 0 4px rgba(99,102,241,.12);}
        .input-wrap input::placeholder{color:#b0bac9;font-size:13px}
        .eye-btn{position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:#94a3b8;font-size:14px;transition:color .15s;background:none;border:none;padding:2px}
        .eye-btn:hover{color:#6366f1}
        .hint{font-size:11px;color:#94a3b8;margin-top:4px;display:flex;align-items:center;gap:4px}
        .field-err{font-size:11.5px;color:#ef4444;margin-top:4px;display:flex;align-items:center;gap:4px}
        .terms-row{display:flex;align-items:flex-start;gap:10px;font-size:13px;color:#475569;margin-bottom:18px}
        .terms-row input[type=checkbox]{width:15px;height:15px;accent-color:#6366f1;cursor:pointer;margin-top:2px;flex-shrink:0}
        .terms-row a{color:#6366f1;font-weight:600;text-decoration:none}
        .terms-row a:hover{text-decoration:underline}
        .btn-primary{width:100%;padding:13px;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;font-family:"Inter",sans-serif;cursor:pointer;transition:all .2s;box-shadow:0 4px 14px rgba(99,102,241,.35);display:flex;align-items:center;justify-content:center;gap:8px;}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(99,102,241,.45)}
        .divider{display:flex;align-items:center;gap:12px;margin:18px 0;font-size:12px;color:#94a3b8}
        .divider::before,.divider::after{content:"";flex:1;height:1px;background:#e2e8f0}
        .btn-google{width:100%;padding:12px 16px;border:1.5px solid #e2e8f0;background:#fff;border-radius:10px;font-size:14px;font-weight:600;color:#374151;font-family:"Inter",sans-serif;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:10px;}
        .btn-google:hover{background:#f8fafc;border-color:#c7d2fe;box-shadow:0 2px 10px rgba(0,0,0,.06)}
        .switch-link{text-align:center;font-size:13.5px;color:#64748b;margin-top:16px}
        .switch-link a{color:#6366f1;font-weight:700;text-decoration:none}
        .switch-link a:hover{text-decoration:underline}
        .copy-note{text-align:center;font-size:11px;color:#cbd5e1;margin-top:20px;line-height:1.6}
        @media(max-width:768px){.auth-left{display:none}.auth-right{padding:28px 16px}}
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
            <h1 class="auth-tagline">Join the<br><span>TechStore</span><br>community</h1>
            <p class="auth-sub">Create a free account and enjoy exclusive deals, fast delivery and AI-secured login protection.</p>
            <div class="auth-features">
                <div class="auth-feat">
                    <div class="auth-feat-icon" style="background:rgba(16,185,129,.2)"><i class="fas fa-brain" style="color:#34d399"></i></div>
                    <span class="auth-feat-text">AI Isolation Forest anomaly detection</span>
                </div>
                <div class="auth-feat">
                    <div class="auth-feat-icon" style="background:rgba(99,102,241,.25)"><i class="fas fa-shield-alt" style="color:#a5b4fc"></i></div>
                    <span class="auth-feat-text">3FA protection for unusual logins</span>
                </div>
                <div class="auth-feat">
                    <div class="auth-feat-icon" style="background:rgba(59,130,246,.2)"><i class="fas fa-gift" style="color:#60a5fa"></i></div>
                    <span class="auth-feat-text">Member-only deals and promotions</span>
                </div>
                <div class="auth-feat">
                    <div class="auth-feat-icon" style="background:rgba(245,158,11,.2)"><i class="fas fa-undo-alt" style="color:#fbbf24"></i></div>
                    <span class="auth-feat-text">Easy returns &amp; refunds</span>
                </div>
            </div>
        </div>
    </div>
    <div class="auth-right">
        <div class="auth-form-wrap">
            <div class="form-head">
                <h2>Create Account</h2>
                <p>Fill in your details to get started</p>
            </div>
            <?php if(session("success")): ?>
                <div class="a-success"><i class="fas fa-check-circle"></i> <?php echo e(session("success")); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="a-error">
                    <strong><i class="fas fa-exclamation-circle"></i> Please fix the following:</strong>
                    <ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                </div>
            <?php endif; ?>
            <form class="register-form" action="<?php echo e(route("register")); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="field">
                    <label for="name">Full name</label>
                    <div class="input-wrap">
                        <i class="fas fa-user fi"></i>
                        <input type="text" id="name" name="name" placeholder="John Doe" value="<?php echo e(old("name")); ?>" required autocomplete="name">
                    </div>
                    <?php $__errorArgs = ["name"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-err"><i class="fas fa-circle-exclamation"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope fi"></i>
                        <input type="email" id="email" name="email" placeholder="your@email.com" value="<?php echo e(old("email")); ?>" required autocomplete="email">
                    </div>
                    <?php $__errorArgs = ["email"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-err"><i class="fas fa-circle-exclamation"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <i class="fas fa-lock fi"></i>
                            <input type="password" id="password" name="password" placeholder="Min 8 chars" required autocomplete="new-password">
                            <button type="button" class="eye-btn" onclick="togglePassword('password',this)" tabindex="-1"><i class="fas fa-eye"></i></button>
                        </div>
                        <?php $__errorArgs = ["password"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-err"><i class="fas fa-circle-exclamation"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="input-wrap">
                            <i class="fas fa-lock fi"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat password" required autocomplete="new-password">
                            <button type="button" class="eye-btn" onclick="togglePassword('password_confirmation',this)" tabindex="-1"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label for="security_question">Security Question <span>*</span></label>
                    <div class="input-wrap">
                        <i class="fas fa-question-circle fi"></i>
                        <select id="security_question" name="security_question">
                            <option value="">— Select a question —</option>
                            <optgroup label="Family">
                                <option value="What was the name of your first pet?" <?php echo e(old("security_question")=="What was the name of your first pet?" ? "selected" : ""); ?>>What was the name of your first pet?</option>
                                <option value="What was your childhood best friend&apos;s name?" <?php echo e(old("security_question")=="What was your childhood best friend\'s name?" ? "selected" : ""); ?>>What was your childhood best friend&apos;s name?</option>
                                <option value="What is your mother&apos;s middle name?" <?php echo e(old("security_question")=="What is your mother\'s middle name?" ? "selected" : ""); ?>>What is your mother&apos;s middle name?</option>
                                <option value="What is the name of your oldest sibling?" <?php echo e(old("security_question")=="What is the name of your oldest sibling?" ? "selected" : ""); ?>>What is the name of your oldest sibling?</option>
                            </optgroup>
                            <optgroup label="Childhood &amp; School">
                                <option value="What was the name of your elementary school?" <?php echo e(old("security_question")=="What was the name of your elementary school?" ? "selected" : ""); ?>>What was the name of your elementary school?</option>
                                <option value="What was the name of your favorite teacher?" <?php echo e(old("security_question")=="What was the name of your favorite teacher?" ? "selected" : ""); ?>>What was the name of your favorite teacher?</option>
                                <option value="What was your childhood nickname?" <?php echo e(old("security_question")=="What was your childhood nickname?" ? "selected" : ""); ?>>What was your childhood nickname?</option>
                                <option value="What was your favorite subject in school?" <?php echo e(old("security_question")=="What was your favorite subject in school?" ? "selected" : ""); ?>>What was your favorite subject in school?</option>
                            </optgroup>
                            <optgroup label="Location">
                                <option value="What city were you born in?" <?php echo e(old("security_question")=="What city were you born in?" ? "selected" : ""); ?>>What city were you born in?</option>
                                <option value="What street did you grow up on?" <?php echo e(old("security_question")=="What street did you grow up on?" ? "selected" : ""); ?>>What street did you grow up on?</option>
                                <option value="What city would you most like to live in?" <?php echo e(old("security_question")=="What city would you most like to live in?" ? "selected" : ""); ?>>What city would you most like to live in?</option>
                            </optgroup>
                            <optgroup label="Interests">
                                <option value="What is your favorite movie?" <?php echo e(old("security_question")=="What is your favorite movie?" ? "selected" : ""); ?>>What is your favorite movie?</option>
                                <option value="What is your favorite food?" <?php echo e(old("security_question")=="What is your favorite food?" ? "selected" : ""); ?>>What is your favorite food?</option>
                                <option value="Who is your favorite athlete/sports player?" <?php echo e(old("security_question")=="Who is your favorite athlete/sports player?" ? "selected" : ""); ?>>Who is your favorite athlete/sports player?</option>
                                <option value="Who is your favorite singer/band?" <?php echo e(old("security_question")=="Who is your favorite singer/band?" ? "selected" : ""); ?>>Who is your favorite singer/band?</option>
                            </optgroup>
                        </select>
                    </div>
                    <?php $__errorArgs = ["security_question"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-err"><i class="fas fa-circle-exclamation"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field">
                    <label for="security_answer">Security Answer <span>*</span></label>
                    <div class="input-wrap">
                        <i class="fas fa-key fi"></i>
                        <input type="text" id="security_answer" name="security_answer" placeholder="Your answer (case-insensitive)" value="<?php echo e(old("security_answer")); ?>" autocomplete="off">
                    </div>
                    <div class="hint"><i class="fas fa-info-circle"></i> Used for 3FA verification when AI detects unusual login behavior</div>
                    <?php $__errorArgs = ["security_answer"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-err"><i class="fas fa-circle-exclamation"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="terms-row">
                    <input type="checkbox" id="terms" name="terms" value="1" required>
                    <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a> of <?php echo e(config("app.name","TechStore")); ?></label>
                </div>
                <button type="submit" class="btn-primary"><i class="fas fa-user-plus"></i> Create Account</button>
                <div class="divider">or</div>
                <button type="button" class="btn-google" onclick="loginWithGoogle()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC04"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Continue with Google
                </button>
            </form>
            <p class="switch-link">Already have an account? <a href="<?php echo e(route("login")); ?>">Sign In</a></p>
            <p class="copy-note">&copy; 2026 TechStore. Secured by AI-based 3FA anomaly detection.</p>
        </div>
    </div>
</div>
<script>
    function togglePassword(id,btn){const i=document.getElementById(id),ic=btn.querySelector("i");i.type=i.type==="password"?(ic.classList.replace("fa-eye","fa-eye-slash"),"text"):(ic.classList.replace("fa-eye-slash","fa-eye"),"password");}
    function loginWithGoogle(){window.location.href="<?php echo e(route("auth.google")); ?>";}
</script>
</body>
</html>
<?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\auth\register.blade.php ENDPATH**/ ?>