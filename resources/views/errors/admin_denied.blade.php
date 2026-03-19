<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Access Denied — Admin Only</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;background:#0a0a1a;overflow:hidden;position:relative}
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 70% 60% at 15% 25%,rgba(245,158,11,.1) 0%,transparent 60%),radial-gradient(ellipse 60% 60% at 85% 75%,rgba(99,102,241,.11) 0%,transparent 60%);pointer-events:none}
.bg-blob{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;animation:floatBlob 9s ease-in-out infinite alternate}
.bb1{width:480px;height:480px;background:rgba(245,158,11,.05);top:-100px;left:-80px;animation-delay:0s}
.bb2{width:380px;height:380px;background:rgba(99,102,241,.07);bottom:-80px;right:-100px;animation-delay:4s}
@keyframes floatBlob{0%{transform:translate(0,0)}100%{transform:translate(25px,20px)}}
.grid-dots{position:fixed;inset:0;background-image:radial-gradient(rgba(255,255,255,.035) 1px,transparent 1px);background-size:32px 32px;pointer-events:none}

.card{position:relative;width:100%;max-width:520px;background:rgba(255,255,255,.03);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid rgba(255,255,255,.07);border-radius:28px;padding:52px 44px;text-align:center;box-shadow:0 32px 80px rgba(0,0,0,.5),inset 0 1px 0 rgba(255,255,255,.05);overflow:hidden}
.card::before{content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);width:55%;height:1px;background:linear-gradient(90deg,transparent,rgba(245,158,11,.7),transparent)}

.icon-wrap{width:88px;height:88px;border-radius:24px;background:linear-gradient(135deg,rgba(245,158,11,.18),rgba(217,119,6,.12));border:1px solid rgba(245,158,11,.3);display:flex;align-items:center;justify-content:center;margin:0 auto 28px;position:relative}
.icon-wrap::after{content:'';position:absolute;inset:-6px;border-radius:30px;border:1px solid rgba(245,158,11,.15);animation:ripple 2.5s ease-in-out infinite}
@keyframes ripple{0%,100%{opacity:.5;transform:scale(1)}50%{opacity:1;transform:scale(1.06)}}
.icon-wrap i{font-size:36px;color:#fbbf24}

.badge{display:inline-flex;align-items:center;gap:6px;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.25);color:#fbbf24;font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;padding:5px 14px;border-radius:20px;margin-bottom:20px}
.title{font-size:26px;font-weight:800;color:#fff;letter-spacing:-.4px;margin-bottom:12px}
.msg{font-size:14px;color:rgba(255,255,255,.45);line-height:1.7;max-width:380px;margin:0 auto 28px}

.info-box{background:rgba(245,158,11,.07);border:1px solid rgba(245,158,11,.18);border-radius:12px;padding:14px 18px;display:flex;align-items:flex-start;gap:12px;text-align:left;margin-bottom:32px}
.info-box i{color:#fbbf24;font-size:14px;margin-top:2px;flex-shrink:0}
.info-box p{font-size:13px;color:rgba(255,255,255,.5);line-height:1.6}
.info-box p strong{color:#fbbf24;font-weight:600}

.btn-row{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.btn{display:inline-flex;align-items:center;gap:8px;padding:13px 24px;border-radius:12px;font-size:14px;font-weight:600;text-decoration:none;transition:all .3s;font-family:'Inter',sans-serif;border:none;cursor:pointer}
.btn-admin{background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;box-shadow:0 8px 24px rgba(99,102,241,.35)}
.btn-admin:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(99,102,241,.5);filter:brightness(1.1)}
.btn-shop{background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;box-shadow:0 8px 24px rgba(245,158,11,.35)}
.btn-shop:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(245,158,11,.5);filter:brightness(1.1)}

.divider{margin:32px 0;height:1px;background:rgba(255,255,255,.06)}
.footer-note{font-size:12px;color:rgba(255,255,255,.2)}
</style>
</head>
<body>
<div class="bg-blob bb1"></div>
<div class="bg-blob bb2"></div>
<div class="grid-dots"></div>

<div class="card">
    <div class="icon-wrap"><i class="fa-solid fa-user-shield"></i></div>
    <div class="badge"><i class="fa-solid fa-crown"></i> Admin Only</div>
    <h1 class="title">Access Restricted</h1>
    <p class="msg">This area is reserved exclusively for administrator accounts. Your current account does not have the required privileges.</p>

    <div class="info-box">
        <i class="fa-solid fa-circle-info"></i>
        <p>You are logged in as a <strong>regular user</strong>. To access the admin panel, please contact your system administrator or sign in with an admin account.</p>
    </div>

    <div class="btn-row">
        <a href="{{ route('login') }}" class="btn btn-admin"><i class="fa-solid fa-user-shield"></i> Sign in as Admin</a>
        <a href="{{ route('home') }}" class="btn btn-shop"><i class="fa-solid fa-bag-shopping"></i> Go to Shop</a>
    </div>

    <div class="divider"></div>
    <p class="footer-note">Access level insufficient &bull; {{ config('app.name') }}</p>
</div>
</body>
</html>