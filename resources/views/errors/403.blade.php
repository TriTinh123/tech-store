<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>403 — Access Denied</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;background:#0a0a1a;overflow:hidden;position:relative}
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 70% 60% at 15% 20%,rgba(239,68,68,.13) 0%,transparent 60%),radial-gradient(ellipse 60% 60% at 85% 80%,rgba(99,102,241,.12) 0%,transparent 60%);pointer-events:none}
.bg-blob{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;animation:floatBlob 9s ease-in-out infinite alternate}
.bb1{width:500px;height:500px;background:rgba(239,68,68,.06);top:-120px;left:-80px;animation-delay:0s}
.bb2{width:400px;height:400px;background:rgba(99,102,241,.07);bottom:-80px;right:-100px;animation-delay:4s}
@keyframes floatBlob{0%{transform:translate(0,0)}100%{transform:translate(25px,20px)}}
.grid-dots{position:fixed;inset:0;background-image:radial-gradient(rgba(255,255,255,.035) 1px,transparent 1px);background-size:32px 32px;pointer-events:none}

.card{position:relative;width:100%;max-width:500px;background:rgba(255,255,255,.03);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid rgba(255,255,255,.07);border-radius:28px;padding:52px 44px;text-align:center;box-shadow:0 32px 80px rgba(0,0,0,.5),inset 0 1px 0 rgba(255,255,255,.05);overflow:hidden}
.card::before{content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);width:55%;height:1px;background:linear-gradient(90deg,transparent,rgba(239,68,68,.7),transparent)}

.icon-wrap{width:88px;height:88px;border-radius:24px;background:linear-gradient(135deg,rgba(239,68,68,.2),rgba(220,38,38,.15));border:1px solid rgba(239,68,68,.3);display:flex;align-items:center;justify-content:center;margin:0 auto 32px;position:relative}
.icon-wrap::after{content:'';position:absolute;inset:-6px;border-radius:30px;border:1px solid rgba(239,68,68,.15);animation:ripple 2.5s ease-in-out infinite}
@keyframes ripple{0%,100%{opacity:.5;transform:scale(1)}50%{opacity:1;transform:scale(1.06)}}
.icon-wrap i{font-size:36px;color:#f87171}

.code{font-size:90px;font-weight:900;line-height:1;letter-spacing:-4px;background:linear-gradient(135deg,#f87171,#ef4444,#dc2626);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:16px}
.title{font-size:24px;font-weight:800;color:#fff;letter-spacing:-.4px;margin-bottom:12px}
.msg{font-size:14px;color:rgba(255,255,255,.45);line-height:1.7;max-width:360px;margin:0 auto 36px}

.btn-row{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.btn{display:inline-flex;align-items:center;gap:8px;padding:13px 24px;border-radius:12px;font-size:14px;font-weight:600;text-decoration:none;transition:all .3s;font-family:'Inter',sans-serif;border:none;cursor:pointer}
.btn-home{background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;box-shadow:0 8px 24px rgba(99,102,241,.35)}
.btn-home:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(99,102,241,.5);filter:brightness(1.1)}
.btn-login{background:rgba(255,255,255,.05);color:rgba(255,255,255,.6);border:1px solid rgba(255,255,255,.1)}
.btn-login:hover{background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.2)}

.divider{margin:32px 0;height:1px;background:rgba(255,255,255,.06)}
.footer-note{font-size:12px;color:rgba(255,255,255,.2)}
</style>
</head>
<body>
<div class="bg-blob bb1"></div>
<div class="bg-blob bb2"></div>
<div class="grid-dots"></div>

<div class="card">
    <div class="icon-wrap"><i class="fa-solid fa-ban"></i></div>
    <div class="code">403</div>
    <h1 class="title">Access Denied</h1>
    <p class="msg">You don't have permission to access this page. Only administrators are authorized to view this resource.</p>

    <div class="btn-row">
        <a href="{{ route('home') }}" class="btn btn-home"><i class="fa-solid fa-house"></i> Back to Home</a>
        <a href="{{ route('login') }}" class="btn btn-login"><i class="fa-solid fa-right-to-bracket"></i> Sign In</a>
    </div>

    <div class="divider"></div>
    <p class="footer-note">Error 403 &mdash; Forbidden &bull; {{ config('app.name') }}</p>
</div>
</body>
</html>