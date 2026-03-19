
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>TechStore — Premium Tech Accessories</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --green:   #00b894;
            --green-d: #009973;
            --green-l: #e6f7f4;
            --blue:    #0984e3;
            --danger:  #e84040;
            --warning: #f8b425;
            --text:    #1a1f2e;
            --text-m:  #64748b;
            --border:  #e8edf2;
            --bg:      #f4f7fa;
            --white:   #ffffff;
            --sh-s:    0 2px 8px rgba(0,0,0,.06);
            --sh-m:    0 6px 24px rgba(0,0,0,.10);
            --sh-l:    0 12px 40px rgba(0,0,0,.16);
            --r:  12px;
            --rs:  8px;
            --rxs: 6px;
        }
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter','Segoe UI',sans-serif; background:var(--bg); color:var(--text); line-height:1.6; scroll-behavior:smooth; }

        /* ─────────── TOPBAR ─────────── */
        .topbar {
            background:linear-gradient(90deg,#0b1a2e,#152540);
            color:rgba(255,255,255,.62); font-size:12px; padding:7px 0;
        }
        .topbar-inner {
            max-width:1300px; margin:0 auto; padding:0 24px;
            display:flex; align-items:center; justify-content:space-between;
        }
        .topbar a { color:rgba(255,255,255,.62); text-decoration:none; transition:color .2s; }
        .topbar a:hover { color:var(--green); }
        .tp-promo { font-weight:700; background:linear-gradient(90deg,var(--green),var(--blue)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }

        /* ─────────── NAVBAR ─────────── */
        .navbar { background:var(--white); box-shadow:0 2px 16px rgba(0,0,0,.08); position:sticky; top:0; z-index:600; }
        .nb-inner { max-width:1300px; margin:0 auto; padding:10px 24px; display:flex; align-items:center; gap:16px; }
        .logo { display:flex; align-items:center; gap:10px; text-decoration:none; flex-shrink:0; }
        .logo img { height:42px; border-radius:8px; }
        .logo-text { font-size:26px; font-weight:900; letter-spacing:-1px; }
        .logo-text .t1 { color:var(--blue); font-style:italic; }
        .logo-text .t2 { color:var(--text); }
        .search-box { flex:1; display:flex; border:2px solid var(--border); border-radius:50px; overflow:hidden; transition:border-color .2s,box-shadow .2s; }
        .search-box:focus-within { border-color:var(--green); box-shadow:0 0 0 3px rgba(0,184,148,.12); }
        .search-box input { flex:1; padding:10px 20px; border:none; outline:none; font-size:14px; font-family:inherit; background:transparent; color:var(--text); }
        .search-box button { padding:10px 24px; background:var(--green); border:none; color:white; cursor:pointer; font-size:15px; transition:background .2s; }
        .search-box button:hover { background:var(--green-d); }
        .nav-actions { display:flex; align-items:center; gap:3px; flex-shrink:0; }
        .nav-btn {
            display:flex; flex-direction:column; align-items:center; gap:1px;
            padding:7px 10px; border-radius:var(--rs); text-decoration:none; color:var(--text-m);
            font-size:11px; font-weight:500; transition:all .2s; position:relative; white-space:nowrap; cursor:pointer;
        }
        .nav-btn i { font-size:19px; }
        .nav-btn:hover { background:var(--green-l); color:var(--green); transform:translateY(-2px); }
        .nav-btn:active { transform:translateY(0); }
        .nav-btn.reg { background:linear-gradient(135deg,var(--green),var(--blue)); color:white; border-radius:50px; padding:8px 16px; flex-direction:row; gap:6px; }
        .nav-btn.reg:hover { opacity:.9; transform:translateY(-2px); box-shadow:0 4px 14px rgba(0,184,148,.4); }
        .cart-badge { position:absolute; top:4px; right:4px; background:var(--danger); color:white; border-radius:50%; width:17px; height:17px; font-size:10px; font-weight:700; display:flex; align-items:center; justify-content:center; }

        /* ─────────── CAT NAV ─────────── */
        .cat-nav { background:var(--text); }
        .cn-inner { max-width:1300px; margin:0 auto; padding:0 24px; display:flex; align-items:center; }
        .ci { position:relative; }
        .ci > a { display:flex; align-items:center; gap:6px; padding:11px 15px; color:rgba(255,255,255,.8); text-decoration:none; font-size:13px; font-weight:500; transition:all .2s; white-space:nowrap; }
        .ci > a:hover, .ci:hover > a { background:var(--green); color:white; }
        .ci i.fa-chevron-down { font-size:9px; }
        .c-drop { display:none; position:absolute; top:100%; left:0; background:white; border-radius:0 0 var(--rs) var(--rs); box-shadow:var(--sh-l); min-width:230px; z-index:999; border-top:3px solid var(--green); overflow:hidden; }
        .ci:hover .c-drop { display:block; animation:fadeDown .18s ease; }
        @keyframes fadeDown { from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:translateY(0);} }
        .c-drop a { display:flex; align-items:center; gap:10px; padding:11px 18px; color:var(--text); text-decoration:none; font-size:13px; border-bottom:1px solid #f5f5f5; transition:all .2s; }
        .c-drop a:last-child { border-bottom:none; }
        .c-drop a i { width:16px; color:var(--green); }
        .c-drop a:hover { background:var(--green-l); color:var(--green); padding-left:24px; }
        .ci-ml { margin-left:auto; }

        /* ─────────── HERO ─────────── */
        .hero { background:linear-gradient(135deg,#0b1a2e 0%,#122b44 50%,#1a3a5c 100%); position:relative; overflow:hidden; }
        .hero::before { content:''; position:absolute; width:600px; height:600px; background:radial-gradient(circle,rgba(0,184,148,.13) 0%,transparent 70%); top:-200px; right:-100px; pointer-events:none; }
        .hero::after  { content:''; position:absolute; width:400px; height:400px; background:radial-gradient(circle,rgba(9,132,227,.1) 0%,transparent 70%); bottom:-100px; left:10%; pointer-events:none; }
        .hero-inner { max-width:1300px; margin:0 auto; padding:64px 24px; display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:center; position:relative; z-index:1; }
        .hero-badge { display:inline-flex; align-items:center; gap:6px; background:rgba(0,184,148,.15); border:1px solid rgba(0,184,148,.35); color:var(--green); padding:5px 14px; border-radius:50px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; margin-bottom:18px; }
        .hero-title { font-size:50px; font-weight:900; color:white; line-height:1.08; margin-bottom:18px; }
        .hl { background:linear-gradient(90deg,var(--green),var(--blue)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
        .hero-desc { color:rgba(255,255,255,.6); font-size:16px; max-width:480px; margin-bottom:32px; }
        .hero-btns { display:flex; gap:12px; flex-wrap:wrap; }
        .btn-hp { padding:13px 28px; background:var(--green); color:white; border:none; border-radius:50px; font-size:14px; font-weight:700; cursor:pointer; text-decoration:none; transition:all .25s; display:inline-flex; align-items:center; gap:8px; font-family:inherit; }
        .btn-hp:hover { background:var(--green-d); transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,184,148,.45); }
        .btn-hp:active { transform:translateY(0); }
        .btn-ho { padding:13px 28px; background:transparent; color:white; border:2px solid rgba(255,255,255,.28); border-radius:50px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; transition:all .25s; display:inline-flex; align-items:center; gap:8px; font-family:inherit; }
        .btn-ho:hover { border-color:white; background:rgba(255,255,255,.12); transform:translateY(-2px); }
        .btn-ho:active { transform:translateY(0); }
        .hero-stats { display:flex; gap:36px; margin-top:44px; padding-top:36px; border-top:1px solid rgba(255,255,255,.1); }
        .hs-n { font-size:30px; font-weight:900; color:white; }
        .hs-l { font-size:11px; color:rgba(255,255,255,.45); text-transform:uppercase; letter-spacing:.5px; margin-top:2px; }
        .hero-cards { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .hc { background:rgba(255,255,255,.07); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,.1); border-radius:var(--r); padding:22px 18px; text-align:center; cursor:pointer; transition:all .3s; text-decoration:none; display:block; }
        .hc:hover { background:rgba(255,255,255,.14); transform:translateY(-5px); border-color:rgba(255,255,255,.25); box-shadow:0 12px 32px rgba(0,0,0,.3); }
        .hc i { font-size:34px; display:block; margin-bottom:10px; transition:transform .3s; }
        .hc:hover i { transform:scale(1.18) rotate(-8deg); }
        .hc-t { color:white; font-size:13px; font-weight:700; }
        .hc-s { color:rgba(255,255,255,.45); font-size:11px; margin-top:3px; }
        .hc.a i{color:#74b9ff;} .hc.b i{color:#55efc4;} .hc.c i{color:#fdcb6e;} .hc.d i{color:#fd79a8;}

        /* ─────────── TRUST BAR ─────────── */
        .trust-bar { background:white; border-bottom:1px solid var(--border); }
        .tb-inner { max-width:1300px; margin:0 auto; padding:0 24px; display:grid; grid-template-columns:repeat(4,1fr); }
        .ti { display:flex; align-items:center; gap:14px; padding:18px 20px; border-right:1px solid var(--border); transition:all .2s; cursor:default; }
        .ti:last-child { border-right:none; }
        .ti:hover { background:var(--green-l); }
        .ti:hover .ti-ico { background:var(--green); }
        .ti:hover .ti-ico i { color:white; }
        .ti:hover .ti-title { color:var(--green); }
        .ti-ico { width:46px; height:46px; border-radius:50%; background:var(--green-l); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .ti-ico i { font-size:19px; color:var(--green); }
        .ti-title { font-size:13px; font-weight:700; }
        .ti-sub { font-size:11px; color:var(--text-m); }

        /* ─────────── SECTIONS ─────────── */
        .sw  { max-width:1300px; margin:0 auto; padding:36px 24px; }
        .sw0 { padding-top:0; }
        .sec-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
        .sec-title { position:relative; padding-left:16px; }
        .sec-title::before { content:''; position:absolute; left:0; top:2px; bottom:2px; width:4px; background:linear-gradient(180deg,var(--green),var(--blue)); border-radius:2px; }
        .sec-title h2 { font-size:20px; font-weight:800; }
        .sec-title p  { font-size:12px; color:var(--text-m); margin-top:2px; }
        .sec-link { display:inline-flex; align-items:center; gap:5px; font-size:13px; color:var(--green); font-weight:600; padding:6px 16px; border:1.5px solid var(--green); border-radius:50px; text-decoration:none; transition:all .2s; }
        .sec-link:hover { background:var(--green); color:white; }

        /* ─────────── CATEGORY CARDS ─────────── */
        .cat-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:16px; }
        .cat-card { background:white; border-radius:var(--r); padding:22px 12px; text-align:center; text-decoration:none; color:var(--text); box-shadow:var(--sh-s); transition:all .25s; border:1.5px solid transparent; cursor:pointer; }
        .cat-card:hover { box-shadow:var(--sh-m); border-color:var(--green); transform:translateY(-6px); }
        .cat-card:hover .cat-ico { background:var(--green); }
        .cat-card:hover .cat-ico i { color:white; }
        .cat-ico { width:58px; height:58px; border-radius:16px; background:var(--green-l); display:flex; align-items:center; justify-content:center; margin:0 auto 12px; transition:all .25s; }
        .cat-ico i { font-size:24px; color:var(--green); transition:color .25s; }
        .cat-name { font-size:12px; font-weight:700; }
        .cat-sub  { font-size:11px; color:var(--text-m); margin-top:2px; }

        /* ─────────── FLASH HEADER ─────────── */
        .flash-row { display:flex; align-items:center; gap:12px; flex:1; }
        .flash-badge { background:linear-gradient(135deg,var(--danger),#c0392b); color:white; padding:4px 12px; border-radius:50px; font-size:12px; font-weight:700; animation:pls 2s infinite; }
        @keyframes pls{0%,100%{transform:scale(1);}50%{transform:scale(1.06);}}
        .cd-wrap { display:flex; align-items:center; gap:5px; margin-left:auto; }
        .cd-n { background:var(--text); color:white; padding:5px 10px; border-radius:var(--rxs); font-size:15px; font-weight:800; min-width:34px; text-align:center; }
        .cd-sep { font-size:17px; font-weight:900; color:var(--text); }

        /* ─────────── PRODUCTS GRID ─────────── */
        .prod-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:16px; }
        .prod-card { background:white; border-radius:var(--r); overflow:hidden; box-shadow:var(--sh-s); border:1.5px solid var(--border); transition:all .3s; position:relative; }
        .prod-card:hover { box-shadow:var(--sh-l); transform:translateY(-6px); border-color:var(--green); }
        .prod-img { position:relative; height:185px; background:#f8fafc; overflow:hidden; display:flex; align-items:center; justify-content:center; }
        .prod-img img { max-width:100%; max-height:100%; object-fit:contain; transition:transform .4s; }
        .prod-card:hover .prod-img img { transform:scale(1.07); }
        .bdg-hot { position:absolute; top:10px; left:10px; background:linear-gradient(135deg,var(--green),var(--blue)); color:white; padding:3px 9px; border-radius:50px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; }
        .bdg-sale { position:absolute; top:10px; right:10px; background:var(--danger); color:white; padding:3px 8px; border-radius:var(--rxs); font-size:11px; font-weight:700; }
        /* Slide-up quick actions */
        .qa { display:none; }
        .prod-body { padding:14px; }
        .prod-name { font-size:13px; font-weight:600; color:var(--text); text-decoration:none; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:37px; margin-bottom:6px; line-height:1.45; }
        .prod-name:hover { color:var(--green); }
        .stars { display:flex; align-items:center; gap:1px; margin-bottom:8px; }
        .stars i { font-size:11px; color:var(--warning); }
        .stars span { font-size:11px; color:var(--text-m); margin-left:4px; }
        .prod-price { display:flex; align-items:baseline; gap:8px; margin-bottom:10px; }
        .price-now { font-size:17px; font-weight:900; color:var(--danger); }
        .price-old { font-size:12px; color:#b2bec3; text-decoration:line-through; }
        .prod-acts { display:flex; gap:6px; }
        .btn-vd { flex:1; padding:7px; background:var(--green-l); color:var(--green); border:1.5px solid rgba(0,184,148,.3); border-radius:var(--rxs); font-size:11px; font-weight:600; cursor:pointer; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:4px; transition:all .2s; font-family:inherit; }
        .btn-vd:hover { background:var(--green); color:white; }
        .btn-cw { flex:1; }
        .btn-cb { width:100%; padding:7px; background:var(--danger); color:white; border:none; border-radius:var(--rxs); font-size:11px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:4px; transition:background .2s; font-family:inherit; }
        .btn-cb:hover { background:#c0392b; }
        .btn-cmp { width:100%; margin-top:7px; padding:6px; background:#f0f5ff; color:#4f7df3; border:1.5px solid #c7d7fc; border-radius:var(--rxs); font-size:11px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:4px; transition:all .2s; font-family:inherit; }
        .btn-cmp:hover { background:#4f7df3; color:white; border-color:#4f7df3; }
        /* View all */
        .va-wrap { text-align:center; padding:28px 0 0; }
        .btn-va { display:inline-flex; align-items:center; gap:8px; padding:12px 32px; background:white; color:var(--green); border:2px solid var(--green); border-radius:50px; font-size:14px; font-weight:700; text-decoration:none; transition:all .25s; }
        .btn-va:hover { background:var(--green); color:white; transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,184,148,.3); }

        /* ─────────── PROMO BANNER ─────────── */
        .promo { background:linear-gradient(135deg,#e67e22,#c0392b); border-radius:var(--r); padding:36px 52px; display:flex; align-items:center; justify-content:space-between; position:relative; overflow:hidden; }
        .promo::before { content:''; position:absolute; width:320px; height:320px; background:rgba(255,255,255,.07); border-radius:50%; right:-80px; top:-100px; pointer-events:none; }
        .promo::after  { content:''; position:absolute; width:200px; height:200px; background:rgba(255,255,255,.05); border-radius:50%; left:40%; bottom:-60px; pointer-events:none; }
        .promo-t { font-size:32px; font-weight:900; color:white; line-height:1.2; position:relative; }
        .promo-s { font-size:14px; color:rgba(255,255,255,.75); margin-top:6px; position:relative; }
        .promo-i { font-size:80px; color:rgba(255,255,255,.25); position:relative; }

        /* ─────────── NEWS ─────────── */
        .news-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; }
        .news-card { background:white; border-radius:var(--r); overflow:hidden; box-shadow:var(--sh-s); border:1px solid var(--border); transition:all .3s; cursor:pointer; text-decoration:none; color:inherit; }
        .news-card:hover { box-shadow:var(--sh-m); transform:translateY(-5px); border-color:var(--green); }
        .news-img { width:100%; height:155px; object-fit:cover; transition:transform .35s; }
        .news-card:hover .news-img { transform:scale(1.06); }
        .news-body { padding:14px; }
        .news-cat { display:inline-block; background:var(--green-l); color:var(--green); font-size:10px; font-weight:700; text-transform:uppercase; padding:2px 9px; border-radius:50px; margin-bottom:8px; }
        .news-ttl { font-size:13px; font-weight:600; color:var(--text); display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; line-height:1.5; }
        .news-card:hover .news-ttl { color:var(--green); }
        .news-meta { font-size:11px; color:var(--text-m); margin-top:8px; display:flex; gap:12px; }
        .news-meta i { color:var(--green); }

        /* ─────────── BRANDS ─────────── */
        .brands-row { display:grid; grid-template-columns:repeat(5,1fr); gap:16px; }
        .brand-card { background:white; border-radius:var(--r); padding:22px; display:flex; align-items:center; justify-content:center; min-height:80px; box-shadow:var(--sh-s); border:1.5px solid var(--border); transition:all .25s; text-decoration:none; cursor:pointer; }
        .brand-card:hover { box-shadow:var(--sh-m); border-color:var(--green); transform:scale(1.06); }
        .brand-card img { max-height:38px; max-width:110px; object-fit:contain; filter:grayscale(1) opacity(.55); transition:filter .25s; }
        .brand-card:hover img { filter:none; }

        /* ─────────── NEWSLETTER ─────────── */
        .nl-wrap { background:linear-gradient(135deg,#0b1a2e,#1a3a5c); border-radius:var(--r); padding:52px 48px; text-align:center; position:relative; overflow:hidden; }
        .nl-wrap::before { content:''; position:absolute; inset:0; background:url("data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E"); }
        .nl-title { font-size:28px; font-weight:900; color:white; margin-bottom:8px; position:relative; }
        .nl-sub   { color:rgba(255,255,255,.55); font-size:14px; margin-bottom:28px; position:relative; }
        .nl-form  { display:flex; max-width:460px; margin:0 auto; border-radius:50px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.3); position:relative; }
        .nl-form input  { flex:1; padding:14px 22px; border:none; outline:none; font-size:14px; font-family:inherit; }
        .nl-form button { padding:14px 28px; background:var(--green); border:none; color:white; font-size:14px; font-weight:700; cursor:pointer; transition:background .2s; font-family:inherit; }
        .nl-form button:hover { background:var(--green-d); }

        /* ─────────── FOOTER ─────────── */
        footer { background:#0b1a2e; }
        .ft-top { max-width:1300px; margin:0 auto; padding:52px 24px 40px; display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:44px; }
        .f-logo { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
        .f-logo img { height:36px; border-radius:6px; }
        .f-logo-t { font-size:22px; font-weight:900; }
        .f-logo-t .t1{color:var(--blue);font-style:italic;} .f-logo-t .t2{color:white;}
        .f-desc { font-size:13px; color:rgba(255,255,255,.5); line-height:1.8; margin-bottom:18px; }
        .f-socials { display:flex; gap:10px; }
        .f-soc { width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,.07); display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,.55); text-decoration:none; font-size:14px; transition:all .2s; }
        .f-soc:hover { background:var(--green); color:white; transform:translateY(-2px); }
        .f-heading { font-size:12px; font-weight:700; color:white; text-transform:uppercase; letter-spacing:.6px; margin-bottom:16px; }
        .f-links { list-style:none; }
        .f-links li { margin-bottom:10px; }
        .f-links a { color:rgba(255,255,255,.5); text-decoration:none; font-size:13px; display:flex; align-items:center; gap:7px; transition:all .2s; }
        .f-links a i { color:var(--green); font-size:11px; width:14px; }
        .f-links a:hover { color:var(--green); padding-left:4px; }
        .ft-bottom { border-top:1px solid rgba(255,255,255,.06); text-align:center; padding:20px 24px; font-size:12px; color:rgba(255,255,255,.28); }
        .ft-bottom .fa-heart { color:#e84040; }

        /* ─────────── ANIMATIONS ─────────── */
        @keyframes flyToCart {
            0%  { opacity:1; transform:translate(0,0) scale(1) rotate(0deg); }
            30% { transform:translate(calc(var(--tx)*.3),calc(var(--ty)*.3)) scale(1.1) rotate(90deg); filter:drop-shadow(0 10px 20px rgba(0,184,148,.5)); }
            70% { transform:translate(calc(var(--tx)*.7),calc(var(--ty)*.7)) scale(0.5) rotate(200deg); filter:blur(1px); }
            100%{ opacity:0; transform:translate(var(--tx),var(--ty)) scale(0.1) rotate(360deg); }
        }
        @keyframes particleFloat { 0%{opacity:1;transform:translate(0,0) scale(1);} 100%{opacity:0;transform:translate(var(--px),var(--py)) scale(0);} }
        @keyframes slideIn  { from{transform:translateX(400px);opacity:0;} to{transform:translateX(0);opacity:1;} }
        @keyframes slideOut { from{transform:translateX(0);opacity:1;} to{transform:translateX(400px);opacity:0;} }
        .flying-item { position:fixed; pointer-events:none; background:linear-gradient(135deg,var(--green),var(--green-d)); width:44px; height:44px; border-radius:50%; color:white; font-size:18px; box-shadow:0 8px 24px rgba(0,184,148,.45); display:flex; align-items:center; justify-content:center; animation:flyToCart .8s cubic-bezier(.25,.46,.45,.94) forwards; }
        .particle { position:fixed; pointer-events:none; border-radius:50%; background:var(--green); animation:particleFloat .8s ease-out forwards; }

        /* ─────────── RESPONSIVE ─────────── */
        @media(max-width:1200px){
            .prod-grid{grid-template-columns:repeat(4,1fr);}
            .cat-grid{grid-template-columns:repeat(3,1fr);}
        }
        @media(max-width:1024px){
            .hero-inner{grid-template-columns:1fr;}
            .hero-cards{display:none;}
            .tb-inner{grid-template-columns:repeat(2,1fr);}
            .ft-top{grid-template-columns:1fr 1fr;}
            .news-grid{grid-template-columns:repeat(2,1fr);}
        }
        @media(max-width:900px){
            .prod-grid{grid-template-columns:repeat(3,1fr);}
            .brands-row{grid-template-columns:repeat(3,1fr);}
        }
        /* ─────────── HAMBURGER ─────────── */
        .hamburger-btn {
            display:none; flex-direction:column; justify-content:center; align-items:center;
            width:40px; height:40px; border:none; background:transparent; cursor:pointer;
            border-radius:8px; gap:5px; flex-shrink:0;
        }
        .hamburger-btn span {
            display:block; width:22px; height:2.5px; background:var(--text);
            border-radius:2px; transition:all .3s;
        }
        .hamburger-btn.active span:nth-child(1){ transform:translateY(7.5px) rotate(45deg); }
        .hamburger-btn.active span:nth-child(2){ opacity:0; transform:scaleX(0); }
        .hamburger-btn.active span:nth-child(3){ transform:translateY(-7.5px) rotate(-45deg); }
        /* Mobile drawer */
        .mobile-drawer {
            display:none; position:fixed; top:0; left:0; right:0; bottom:0;
            z-index:9999; pointer-events:none;
        }
        .mobile-drawer.open { display:block; pointer-events:all; }
        .drawer-overlay {
            position:absolute; inset:0; background:rgba(0,0,0,.5);
            opacity:0; transition:opacity .3s;
        }
        .mobile-drawer.open .drawer-overlay { opacity:1; }
        .drawer-panel {
            position:absolute; top:0; left:0; bottom:0; width:280px;
            background:white; box-shadow:4px 0 24px rgba(0,0,0,.2);
            transform:translateX(-100%); transition:transform .3s cubic-bezier(.4,0,.2,1);
            overflow-y:auto; display:flex; flex-direction:column;
        }
        .mobile-drawer.open .drawer-panel { transform:translateX(0); }
        .drawer-header {
            background:linear-gradient(135deg,#0b1a2e,#152540);
            padding:20px 16px; display:flex; align-items:center; justify-content:space-between;
        }
        .drawer-logo { font-size:20px; font-weight:900; letter-spacing:-0.5px; }
        .drawer-logo .t1{color:var(--blue);font-style:italic;} .drawer-logo .t2{color:white;}
        .drawer-close {
            width:32px; height:32px; border:none; background:rgba(255,255,255,.12);
            color:white; border-radius:50%; cursor:pointer; font-size:16px;
            display:flex; align-items:center; justify-content:center;
        }
        .drawer-nav { flex:1; padding:8px 0; }
        .drawer-nav a {
            display:flex; align-items:center; gap:12px; padding:13px 20px;
            color:var(--text); text-decoration:none; font-size:14px; font-weight:500;
            border-bottom:1px solid var(--border); transition:all .2s;
        }
        .drawer-nav a:hover { background:var(--green-l); color:var(--green); }
        .drawer-nav a i { width:18px; color:var(--green); font-size:15px; }
        .drawer-sub-title {
            padding:10px 20px 6px; font-size:11px; font-weight:700; color:var(--text-m);
            text-transform:uppercase; letter-spacing:.6px; background:var(--bg);
        }
        .drawer-footer { padding:16px; border-top:1px solid var(--border); display:flex; gap:8px; }
        .drawer-footer a { flex:1; padding:10px; border-radius:8px; text-align:center; font-size:13px; font-weight:600; text-decoration:none; }
        .drawer-btn-login  { background:var(--green-l); color:var(--green); border:1.5px solid rgba(0,184,148,.3); }
        .drawer-btn-signup { background:var(--green); color:white; }
        @media(max-width:768px){
            .hamburger-btn{ display:flex; }
            .cat-nav{ display:none; }
            .hero-title{font-size:32px;}
            .hero-inner{padding:40px 16px;}
            .hero-stats{gap:20px; flex-wrap:wrap;}
            .hs-n{font-size:22px;}
            .topbar{display:none;}
            /* navbar */
            .nb-inner{padding:8px 12px; gap:8px; flex-wrap:wrap;}
            .search-box{order:3; flex:0 0 100%; border-radius:8px;}
            .logo-text{font-size:20px;}
            .nav-btn span{display:none;}
            .nav-btn{padding:6px 8px;}
            .nav-btn i{font-size:17px;}
            .nav-btn.reg span{display:inline;}
            .nav-btn.reg{padding:7px 12px; font-size:12px;}
            /* sections */
            .sw{padding:24px 12px;}
            .sec-hdr{flex-wrap:wrap; gap:10px;}
            /* trust bar */
            .tb-inner{grid-template-columns:1fr 1fr;}
            .ti{padding:12px 14px; gap:10px;}
            .ti-ico{width:38px; height:38px;}
            .ti-ico i{font-size:16px;}
            /* promo */
            .promo{padding:24px 20px; flex-direction:column; gap:16px; text-align:center;}
            .promo-t{font-size:22px;}
            .promo-i{display:none;}
            /* footer */
            .ft-top{grid-template-columns:1fr; gap:24px; padding:32px 16px 24px;}
            .ft-bottom{font-size:11px;}
            /* newsletter */
            .nl-wrap{padding:36px 20px;}
            .nl-title{font-size:22px;}
            .nl-form{flex-direction:column; border-radius:12px; overflow:visible; box-shadow:none; gap:8px;}
            .nl-form input{border-radius:50px; padding:12px 18px; border:2px solid var(--border);}
            .nl-form button{border-radius:50px; padding:12px 18px;}
        }
        @media(max-width:600px){
            .prod-grid{grid-template-columns:repeat(2,1fr); gap:10px;}
            .cat-grid{grid-template-columns:repeat(3,1fr); gap:10px;}
            .news-grid{grid-template-columns:1fr;}
            .brands-row{grid-template-columns:repeat(2,1fr);}
            .hero-title{font-size:26px; line-height:1.15;}
            .hero-badge{font-size:10px;}
            .hero-desc{font-size:13px;}
            .hero-btns{gap:8px;}
            .btn-hp,.btn-ho{padding:10px 20px; font-size:13px;}
            .hero-stats{gap:16px;}
            .hs-n{font-size:20px;}
            .tb-inner{grid-template-columns:1fr 1fr;}
            .ti-sub{display:none;}
            .prod-img{height:140px;}
            .price-now{font-size:15px;}
            .prod-body{padding:10px;}
            .cat-card{padding:16px 8px;}
            .cat-ico{width:46px; height:46px; border-radius:12px;}
            .cat-ico i{font-size:20px;}
            .cat-name{font-size:11px;}
            .flash-row{flex-wrap:wrap; gap:6px;}
            .cd-wrap{margin-left:0;}
            /* hide compare btn on mobile */
            .btn-cmp{display:none;}
        }
        @media(max-width:400px){
            .cat-grid{grid-template-columns:repeat(2,1fr);}
            .prod-grid{grid-template-columns:repeat(2,1fr);}
            .logo-text{font-size:17px;}
        }
    </style>
</head>
<body>


<div class="topbar">
    <div class="topbar-inner">
        <div style="display:flex;align-items:center;gap:20px">
            <span><i class="fas fa-phone-alt" style="color:var(--green)"></i> 0876-211-629</span>
            <span><i class="fas fa-envelope" style="color:var(--green)"></i> support@techstore.com</span>
        </div>
        <span class="tp-promo"><i class="fas fa-bolt"></i> Free shipping on orders over $50</span>
        <div style="display:flex;gap:16px">
            <a href="#">Return Policy</a>
            <a href="#">Shopping Guide</a>
        </div>
    </div>
</div>


<nav class="navbar">
    <div class="nb-inner">
        <a href="<?php echo e(route('home')); ?>" class="logo">
            <img src="/images/logo.jpg" alt="TechStore">
            <span class="logo-text"><span class="t1">Tech</span><span class="t2">Store</span></span>
        </a>
        <form class="search-box" action="<?php echo e(route('products.index')); ?>" method="GET">
            <input type="text" name="search" placeholder="Search products, brands..." value="<?php echo e(request('search','')); ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
        <div class="nav-actions">
            <?php if(auth()->guard()->check()): ?>
                <?php $user = Auth::user(); ?>
                <a href="<?php echo e(route('profile.show')); ?>" class="nav-btn">
                    <?php if($user->face_photo): ?>
                        <img src="<?php echo e($user->face_photo); ?>" style="width:26px;height:26px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.5);vertical-align:middle;margin-right:3px">
                    <?php else: ?>
                        <i class="fas fa-user-circle"></i>
                    <?php endif; ?>
                    <span><?php echo e(Str::limit($user->name,10)); ?></span>
                </a>
                <a href="<?php echo e(route('wishlist')); ?>" class="nav-btn"><i class="fas fa-heart"></i><span>Wishlist</span></a>
                <a href="<?php echo e(route('notifications.index')); ?>" class="nav-btn"><i class="fas fa-bell"></i><span>Notifications</span></a>
                <a href="<?php echo e(route('logout')); ?>" class="nav-btn" onclick="event.preventDefault();document.getElementById('lf').submit()"><i class="fas fa-sign-out-alt"></i><span>Sign Out</span></a>
                <form id="lf" action="<?php echo e(route('logout')); ?>" method="POST" style="display:none"><?php echo csrf_field(); ?></form>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="nav-btn"><i class="fas fa-sign-in-alt"></i><span>Sign In</span></a>
                <a href="<?php echo e(route('register')); ?>" class="nav-btn reg"><i class="fas fa-user-plus"></i><span>Sign Up</span></a>
            <?php endif; ?>
            <a href="<?php echo e(route('cart.index')); ?>" class="nav-btn" id="cart-nav-btn" style="position:relative">
                <i class="fas fa-shopping-cart"></i><span>Cart</span>
                <?php $cartCount = array_sum(session()->get('cart',[])); ?>
                <?php if($cartCount > 0): ?><span class="cart-badge" id="cart-badge"><?php echo e($cartCount); ?></span><?php endif; ?>
            </a>
        </div>
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>


<div class="mobile-drawer" id="mobileDrawer">
    <div class="drawer-overlay" id="drawerOverlay"></div>
    <div class="drawer-panel">
        <div class="drawer-header">
            <span class="drawer-logo"><span class="t1">Tech</span><span class="t2">Store</span></span>
            <button class="drawer-close" id="drawerClose"><i class="fas fa-times"></i></button>
        </div>
        <nav class="drawer-nav">
            <a href="<?php echo e(route('home')); ?>"><i class="fas fa-home"></i> Home</a>
            <a href="<?php echo e(route('about')); ?>"><i class="fas fa-info-circle"></i> About</a>
            <div class="drawer-sub-title">Products</div>
            <a href="<?php echo e(route('products.index')); ?>?category=gaming"><i class="fas fa-gamepad"></i> Gaming</a>
            <a href="<?php echo e(route('products.index')); ?>?category=peripherals"><i class="fas fa-mouse"></i> Peripherals</a>
            <a href="<?php echo e(route('products.index')); ?>?category=storage"><i class="fas fa-hdd"></i> Storage &amp; Connectivity</a>
            <a href="<?php echo e(route('products.index')); ?>?category=power"><i class="fas fa-plug"></i> Power &amp; Cooling</a>
            <a href="<?php echo e(route('products.index')); ?>?category=protection"><i class="fas fa-shield-alt"></i> Protection &amp; Decor</a>
            <a href="<?php echo e(route('products.index')); ?>?category=office"><i class="fas fa-briefcase"></i> Office</a>
            <a href="<?php echo e(route('products.index')); ?>" style="font-weight:700;color:var(--green) !important"><i class="fas fa-star"></i> All Products</a>
            <div class="drawer-sub-title">Support</div>
            <a href="<?php echo e(route('contact')); ?>"><i class="fas fa-question-circle"></i> Help Center</a>
            <a href="<?php echo e(route('contact')); ?>"><i class="fas fa-envelope"></i> Contact</a>
            <a href="<?php echo e(route('news')); ?>"><i class="fas fa-newspaper"></i> News</a>
            <?php if(auth()->guard()->check()): ?>
            <div class="drawer-sub-title">My Account</div>
            <a href="<?php echo e(route('profile.show')); ?>"><i class="fas fa-user-circle"></i> My Profile</a>
            <a href="<?php echo e(route('orders.index')); ?>"><i class="fas fa-box-open"></i> My Orders</a>
            <a href="<?php echo e(route('wishlist')); ?>"><i class="fas fa-heart"></i> Wishlist</a>
            <a href="<?php echo e(route('notifications.index')); ?>"><i class="fas fa-bell"></i> Notifications</a>
            <a href="<?php echo e(route('compare.index')); ?>"><i class="fas fa-balance-scale"></i> Compare</a>
            <a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();document.getElementById('lf').submit()"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
            <?php else: ?>
            <div class="drawer-footer">
                <a href="<?php echo e(route('login')); ?>" class="drawer-btn-login"><i class="fas fa-sign-in-alt"></i> Sign In</a>
                <a href="<?php echo e(route('register')); ?>" class="drawer-btn-signup"><i class="fas fa-user-plus"></i> Sign Up</a>
            </div>
            <?php endif; ?>
        </nav>
    </div>
</div>


<div class="cat-nav">
    <div class="cn-inner">
        <div class="ci"><a href="<?php echo e(route('home')); ?>"><i class="fas fa-home"></i> Home</a></div>
        <div class="ci"><a href="<?php echo e(route('about')); ?>"><i class="fas fa-info-circle"></i> About</a></div>
        <div class="ci">
            <a href="#"><i class="fas fa-th-large"></i> Products <i class="fas fa-chevron-down"></i></a>
            <div class="c-drop">
                <a href="<?php echo e(route('products.index')); ?>?category=gaming"><i class="fas fa-gamepad"></i> Gaming</a>
                <a href="<?php echo e(route('products.index')); ?>?category=peripherals"><i class="fas fa-mouse"></i> Peripherals</a>
                <a href="<?php echo e(route('products.index')); ?>?category=storage"><i class="fas fa-hdd"></i> Storage &amp; Connectivity</a>
                <a href="<?php echo e(route('products.index')); ?>?category=power"><i class="fas fa-plug"></i> Power &amp; Cooling</a>
                <a href="<?php echo e(route('products.index')); ?>?category=protection"><i class="fas fa-shield-alt"></i> Protection &amp; Decor</a>
                <a href="<?php echo e(route('products.index')); ?>?category=office"><i class="fas fa-briefcase"></i> Office</a>
                <a href="<?php echo e(route('products.index')); ?>" style="font-weight:700;color:var(--green) !important"><i class="fas fa-star"></i> View all products</a>
            </div>
        </div>
        <div class="ci">
            <a href="#"><i class="fas fa-headset"></i> Support <i class="fas fa-chevron-down"></i></a>
            <div class="c-drop">
                <a href="<?php echo e(route('contact')); ?>"><i class="fas fa-question-circle"></i> Help Center</a>
                <a href="#" onclick="event.preventDefault();if(typeof openChat==='function')openChat()"><i class="fas fa-comments"></i> Chat with TechStore</a>
            </div>
        </div>
        <div class="ci"><a href="<?php echo e(route('contact')); ?>"><i class="fas fa-envelope"></i> Contact</a></div>
        <div class="ci"><a href="<?php echo e(route('news')); ?>"><i class="fas fa-newspaper"></i> News</a></div>
        <?php if(auth()->guard()->check()): ?>
        <div class="ci ci-ml"><a href="<?php echo e(route('orders.index')); ?>"><i class="fas fa-box-open"></i> My Orders</a></div>
        <div class="ci"><a href="<?php echo e(route('compare.index')); ?>"><i class="fas fa-balance-scale"></i> Compare</a></div>
        <?php endif; ?>
    </div>
</div>


<section class="hero">
    <div class="hero-inner">
        <div>
            <div class="hero-badge"><i class="fas fa-bolt"></i> Top Tech 2026</div>
            <h1 class="hero-title">TECH<br><span class="hl">ACCESSORIES</span><br>PREMIUM QUALITY</h1>
            <p class="hero-desc">Explore thousands of genuine tech products — from gaming gear to office essentials. Fast delivery, manufacturer warranty.</p>
            <div class="hero-btns">
                <a href="<?php echo e(route('products.index')); ?>" class="btn-hp"><i class="fas fa-shopping-bag"></i> Buy Now</a>
                <a href="#san-pham-moi" class="btn-ho"><i class="fas fa-fire"></i> View hot deals</a>
            </div>
            <div class="hero-stats">
                <div><div class="hs-n">500+</div><div class="hs-l">Products</div></div>
                <div><div class="hs-n">10K+</div><div class="hs-l">Customer</div></div>
                <div><div class="hs-n">4.9★</div><div class="hs-l">Rating</div></div>
                <div><div class="hs-n">24/7</div><div class="hs-l">Support</div></div>
            </div>
        </div>
        <div class="hero-cards">
            <a href="<?php echo e(route('products.index')); ?>?category=gaming" class="hc a"><i class="fas fa-gamepad"></i><div class="hc-t">Gaming Gear</div><div class="hc-s">Top performance</div></a>
            <a href="<?php echo e(route('products.index')); ?>?category=peripherals" class="hc b"><i class="fas fa-keyboard"></i><div class="hc-t">Mechanical Keyboard</div><div class="hc-s">Amazing typing feel</div></a>
            <a href="<?php echo e(route('products.index')); ?>?category=audio" class="hc c"><i class="fas fa-headphones-alt"></i><div class="hc-t">Hi-Fi Headphones</div><div class="hc-s">Vivid sound</div></a>
            <a href="<?php echo e(route('products.index')); ?>?category=monitor" class="hc d"><i class="fas fa-desktop"></i><div class="hc-t">4K Monitor</div><div class="hc-s">Ultra-sharp display</div></a>
        </div>
    </div>
</section>


<div class="trust-bar">
    <div class="tb-inner">
        <div class="ti"><div class="ti-ico"><i class="fas fa-shipping-fast"></i></div><div><div class="ti-title">Express Delivery</div><div class="ti-sub">Within 24 hours nationwide</div></div></div>
        <div class="ti"><div class="ti-ico"><i class="fas fa-award"></i></div><div><div class="ti-title">Genuine Warranty</div><div class="ti-sub">12–36 months per product</div></div></div>
        <div class="ti"><div class="ti-ico"><i class="fas fa-undo-alt"></i></div><div><div class="ti-title">Easy Returns</div><div class="ti-sub">30-day money back, no questions asked</div></div></div>
        <div class="ti"><div class="ti-ico"><i class="fas fa-lock"></i></div><div><div class="ti-title">Secure Checkout</div><div class="ti-sub">SSL 256-bit encryption</div></div></div>
    </div>
</div>


<div class="sw">
    <div class="sec-hdr">
        <div class="sec-title"><h2>Featured Categories</h2><p>Find products in your favorite category</p></div>
        <a href="<?php echo e(route('products.index')); ?>" class="sec-link">All Categories <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="cat-grid">
        <a href="<?php echo e(route('products.index')); ?>?category=gaming"      class="cat-card"><div class="cat-ico"><i class="fas fa-gamepad"></i></div><div class="cat-name">Gaming</div><div class="cat-sub">Gear &amp; Setup</div></a>
        <a href="<?php echo e(route('products.index')); ?>?category=peripherals" class="cat-card"><div class="cat-ico"><i class="fas fa-mouse"></i></div><div class="cat-name">Peripherals</div><div class="cat-sub">Mouse, keyboard</div></a>
        <a href="<?php echo e(route('products.index')); ?>?category=storage"     class="cat-card"><div class="cat-ico"><i class="fas fa-hdd"></i></div><div class="cat-name">Storage</div><div class="cat-sub">SSD, USB, HDD</div></a>
        <a href="<?php echo e(route('products.index')); ?>?category=power"       class="cat-card"><div class="cat-ico"><i class="fas fa-plug"></i></div><div class="cat-name">Power &amp; Cooling</div><div class="cat-sub">PSU, cooler</div></a>
        <a href="<?php echo e(route('products.index')); ?>?category=protection"  class="cat-card"><div class="cat-ico"><i class="fas fa-shield-alt"></i></div><div class="cat-name">Protection</div><div class="cat-sub">Cases &amp; accessories</div></a>
        <a href="<?php echo e(route('products.index')); ?>?category=office"      class="cat-card"><div class="cat-ico"><i class="fas fa-briefcase"></i></div><div class="cat-name">Office</div><div class="cat-sub">High performance</div></a>
    </div>
</div>


<div class="sw sw0" id="san-pham-moi">
    <div class="sec-hdr">
        <div class="flash-row">
            <div class="sec-title" style="padding-left:0"><h2>⚡ Daily Flash Sale</h2><p>5 deals refreshed every day — ends at midnight</p></div>
            <span class="flash-badge">HOT</span>
            <div class="cd-wrap">
                <span class="cd-n" id="cd-h">00</span><span class="cd-sep">:</span>
                <span class="cd-n" id="cd-m">00</span><span class="cd-sep">:</span>
                <span class="cd-n" id="cd-s">00</span>
            </div>
        </div>
        <a href="<?php echo e(route('products.index')); ?>" class="sec-link" style="margin-left:24px">View all <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="prod-grid">
        <?php $__empty_1 = true; $__currentLoopData = $flashSaleProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $discountPct  = $flashSaleDiscounts[$product->id] ?? 0;
            $flashPrice   = round($product->price * (1 - $discountPct / 100), 2);
        ?>
        <div class="prod-card">
            <div class="prod-img">
                <span class="bdg-sale" style="background:linear-gradient(135deg,#e84040,#c0392b)">⚡ -<?php echo e($discountPct); ?>%</span>
                <img src="<?php echo e($product->image ?? 'https://via.placeholder.com/200x200?text='.urlencode($product->name)); ?><?php echo e($product->image ? '?v='.md5($product->updated_at) : ''); ?>" alt="<?php echo e($product->name); ?>" loading="lazy">
            </div>
            <div class="prod-body">
                <a href="<?php echo e(route('product.show', $product->id)); ?>" class="prod-name"><?php echo e($product->name); ?></a>
                <?php if($product->rating): ?>
                <div class="stars"><?php for($i=0;$i<5;$i++): ?><i class="<?php echo e($i < $product->rating ? 'fas' : 'far'); ?> fa-star"></i><?php endfor; ?><span>(<?php echo e($product->reviews_count ?? 0); ?>)</span></div>
                <?php endif; ?>
                <div class="prod-price">
                    <span class="price-now">$<?php echo e(number_format($flashPrice, 2)); ?></span>
                    <span class="price-old">$<?php echo e(number_format($product->price, 2)); ?></span>
                </div>
                <div class="prod-acts">
                    <a href="<?php echo e(route('product.show', $product->id)); ?>" class="btn-vd"><i class="fas fa-eye"></i> Details</a>
                    <div class="btn-cw"><form action="<?php echo e(route('cart.add', $product->id)); ?>" method="POST" class="add-to-cart-form"><?php echo csrf_field(); ?><button type="submit" class="btn-cb"><i class="fas fa-shopping-cart"></i> Cart</button></form></div>
                </div>
                <?php if(auth()->guard()->check()): ?><button onclick="toggleCompare(<?php echo e($product->id); ?>, this)" data-id="<?php echo e($product->id); ?>" class="btn-cmp"><i class="fas fa-balance-scale"></i> Compare</button><?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-m)">No flash sale products today</p>
        <?php endif; ?>
    </div>
    <div class="va-wrap"><a href="<?php echo e(route('products.index')); ?>" class="btn-va"><i class="fas fa-th-large"></i> View all products</a></div>
</div>


<div class="sw sw0">
    <div class="promo">
        <div><div class="promo-t">ALWAYS GENUINE<br>ALWAYS RELIABLE</div><div class="promo-s">100% genuine guarantee — full refund if not authentic</div></div>
        <i class="fas fa-medal promo-i"></i>
    </div>
</div>


<div class="sw sw0">
    <div class="sec-hdr">
        <div class="sec-title"><h2>⭐ Featured Products</h2><p>Best sellers this week</p></div>
        <a href="<?php echo e(route('products.index')); ?>" class="sec-link">View more <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="prod-grid">
        <?php $__empty_1 = true; $__currentLoopData = $products->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="prod-card">
            <div class="prod-img">
                <?php if($product->discount_percentage > 0): ?><span class="bdg-sale">-<?php echo e($product->discount_percentage); ?>%</span><?php endif; ?>
                <img src="<?php echo e($product->image ?? 'https://via.placeholder.com/200x200?text='.urlencode($product->name)); ?><?php echo e($product->image ? '?v='.md5($product->updated_at) : ''); ?>" alt="<?php echo e($product->name); ?>" loading="lazy">
            </div>
            <div class="prod-body">
                <a href="<?php echo e(route('product.show', $product->id)); ?>" class="prod-name"><?php echo e($product->name); ?></a>
                <?php if($product->rating): ?>
                <div class="stars"><?php for($i=0;$i<5;$i++): ?><i class="<?php echo e($i < $product->rating ? 'fas' : 'far'); ?> fa-star"></i><?php endfor; ?></div>
                <?php endif; ?>
                <div class="prod-price">
                    <span class="price-now">$<?php echo e(number_format($product->price, 2)); ?></span>
                </div>
                <div class="prod-acts">
                    <a href="<?php echo e(route('product.show', $product->id)); ?>" class="btn-vd"><i class="fas fa-eye"></i> Details</a>
                    <div class="btn-cw"><form action="<?php echo e(route('cart.add', $product->id)); ?>" method="POST" class="add-to-cart-form"><?php echo csrf_field(); ?><button type="submit" class="btn-cb"><i class="fas fa-shopping-cart"></i> Cart</button></form></div>
                </div>
                <?php if(auth()->guard()->check()): ?><button onclick="toggleCompare(<?php echo e($product->id); ?>, this)" data-id="<?php echo e($product->id); ?>" class="btn-cmp"><i class="fas fa-balance-scale"></i> Compare</button><?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-m)">No products yet</p>
        <?php endif; ?>
    </div>
    <div class="va-wrap"><a href="<?php echo e(route('products.index')); ?>" class="btn-va"><i class="fas fa-arrow-right"></i> View all featured products</a></div>
</div>


<div class="sw sw0">
    <div class="sec-hdr">
        <div class="sec-title"><h2>📰 Tech News</h2><p>Stay up to date with the latest trends</p></div>
        <a href="<?php echo e(route('news')); ?>" class="sec-link">All news <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="news-grid">
        <a href="<?php echo e(route('news')); ?>" class="news-card"><div class="news-img-wrap" style="overflow:hidden"><img src="/images/sptt1.jpg" alt="Gaming" class="news-img"></div><div class="news-body"><span class="news-cat">Gaming</span><span class="news-ttl">Top 5 mechanical gaming keyboards in 2026 — Which one should you choose?</span><div class="news-meta"><span><i class="fas fa-calendar-alt"></i> 03/02/2026</span><span><i class="fas fa-user"></i> TechStore</span></div></div></a>
        <a href="<?php echo e(route('news')); ?>" class="news-card"><div class="news-img-wrap" style="overflow:hidden"><img src="/images/sptt2.jpg" alt="Review" class="news-img"></div><div class="news-body"><span class="news-cat">Review</span><span class="news-ttl">Wireless vs wired gaming mouse — Which is better for gamers in 2026?</span><div class="news-meta"><span><i class="fas fa-calendar-alt"></i> 02/02/2026</span><span><i class="fas fa-user"></i> TechStore</span></div></div></a>
        <a href="<?php echo e(route('news')); ?>" class="news-card"><div class="news-img-wrap" style="overflow:hidden"><img src="/images/sptt3.jpg" alt="Setup" class="news-img"></div><div class="news-body"><span class="news-cat">Setup</span><span class="news-ttl">Professional gaming headset — Best choices for streamers in 2026</span><div class="news-meta"><span><i class="fas fa-calendar-alt"></i> 31/01/2026</span><span><i class="fas fa-user"></i> TechStore</span></div></div></a>
        <a href="<?php echo e(route('news')); ?>" class="news-card"><div class="news-img-wrap" style="overflow:hidden"><img src="/images/sptt4.jpg" alt="Technology" class="news-img"></div><div class="news-body"><span class="news-cat">Technology</span><span class="news-ttl">Latest gaming monitors in 2026 — High refresh rate and 4K performance</span><div class="news-meta"><span><i class="fas fa-calendar-alt"></i> 29/01/2026</span><span><i class="fas fa-user"></i> TechStore</span></div></div></a>
    </div>
</div>


<div class="sw sw0">
    <div class="sec-hdr">
        <div class="sec-title"><h2>Trusted Brands</h2><p>Genuine products from the world's leading brands</p></div>
    </div>
    <div class="brands-row">
        <a href="<?php echo e(route('products.index')); ?>?brand=Logitech" class="brand-card"><img src="/images/Logitech.png" alt="Logitech"></a>
        <a href="<?php echo e(route('products.index')); ?>?brand=ASUS" class="brand-card"><img src="/images/azus.png" alt="ASUS"></a>
        <a href="<?php echo e(route('products.index')); ?>?brand=Razer" class="brand-card"><img src="/images/razer.png" alt="Razer"></a>
        <a href="<?php echo e(route('products.index')); ?>?brand=Corsair" class="brand-card"><img src="/images/corsair.png" alt="Corsair"></a>
        <a href="<?php echo e(route('products.index')); ?>?brand=SteelSeries" class="brand-card"><img src="/images/steel.png" alt="SteelSeries"></a>
    </div>
</div>


<div class="sw sw0" style="padding-bottom:48px">
    <div class="nl-wrap">
        <h2 class="nl-title">📬 Sign Up for Exclusive Deals</h2>
        <p class="nl-sub">Get exclusive promotions and new product alerts via email</p>
        <div class="nl-form">
            <input type="email" placeholder="Enter your email address...">
            <button type="button"><i class="fas fa-paper-plane"></i> Sign Up</button>
        </div>
    </div>
</div>


<footer>
    <div class="ft-top">
        <div>
            <div class="f-logo"><img src="/images/logo.jpg" alt="TechStore"><span class="f-logo-t"><span class="t1">Tech</span><span class="t2">Store</span></span></div>
            <p class="f-desc">TechStore — Leading premium tech accessories store. 100% genuine products, fast delivery, 24/7 support.</p>
            <div class="f-socials">
                <a href="#" class="f-soc"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="f-soc"><i class="fab fa-youtube"></i></a>
                <a href="#" class="f-soc"><i class="fab fa-instagram"></i></a>
                <a href="#" class="f-soc"><i class="fab fa-tiktok"></i></a>
                <a href="#" class="f-soc"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
        <div>
            <div class="f-heading">Customer Support</div>
            <ul class="f-links">
                <li><a href="#"><i class="fas fa-redo"></i> Return Policy</a></li>
                <li><a href="#"><i class="fas fa-shield-alt"></i> Privacy Policy</a></li>
                <li><a href="#"><i class="fas fa-credit-card"></i> Payment Guide</a></li>
                <li><a href="#"><i class="fas fa-shopping-bag"></i> Shopping Guide</a></li>
                <li><a href="#"><i class="fas fa-truck"></i> Shipping Policy</a></li>
            </ul>
        </div>
        <div>
            <div class="f-heading">About TechStore</div>
            <ul class="f-links">
                <li><a href="<?php echo e(route('about')); ?>"><i class="fas fa-building"></i> About Us</a></li>
                <li><a href="#"><i class="fas fa-users"></i> Careers</a></li>
                <li><a href="<?php echo e(route('news')); ?>"><i class="fas fa-newspaper"></i> News</a></li>
                <li><a href="<?php echo e(route('contact')); ?>"><i class="fas fa-envelope"></i> Contact</a></li>
            </ul>
        </div>
        <div>
            <div class="f-heading">Contact</div>
            <ul class="f-links">
                <li><a href="#"><i class="fas fa-map-marker-alt"></i> Hem 1, Pham Ngu Lao St, Can Tho</a></li>
                <li><a href="tel:19001234"><i class="fas fa-phone-alt"></i> 0876-211-629</a></li>
                <li><a href="mailto:support@techstore.com"><i class="fas fa-envelope"></i> support@techstore.com</a></li>
                <li><a href="#"><i class="fas fa-clock"></i> 8:00 AM – 10:00 PM daily</a></li>
            </ul>
        </div>
    </div>
    <div class="ft-bottom">&copy; 2026 TechStore — All rights reserved. Built with <i class="fas fa-heart"></i> in Vietnam.</div>
</footer>


<?php if(auth()->guard()->check()): ?>
<div id="compare-bar" style="display:none;position:fixed;bottom:0;left:0;right:0;background:#1a2540;color:#fff;padding:12px 20px;z-index:1000;align-items:center;gap:12px;justify-content:center;box-shadow:0 -4px 20px rgba(0,0,0,.4)">
    <i class="fas fa-balance-scale" style="color:#60a5fa;font-size:16px"></i>
    <span style="font-size:14px">Comparing: <strong id="compare-count">0</strong> products</span>
    <a href="<?php echo e(route('compare.index')); ?>" style="background:#3b82f6;color:#fff;padding:7px 18px;border-radius:50px;text-decoration:none;font-size:13px;font-weight:700">Compare ngay</a>
    <button onclick="clearCompare()" style="background:#e84040;color:#fff;border:none;padding:7px 14px;border-radius:50px;cursor:pointer;font-size:12px;font-weight:700">Clear all</button>
</div>
<?php endif; ?>


<script>
// Countdown timer (flash sale — counts down to midnight)
(function(){
    var end = new Date(); end.setHours(23,59,59,0);
    function tick(){
        var now=new Date(), diff=end-now;
        if(diff<0){end.setDate(end.getDate()+1); diff=end-now;}
        var h=Math.floor(diff/3600000), m=Math.floor((diff%3600000)/60000), s=Math.floor((diff%60000)/1000);
        var hEl=document.getElementById('cd-h'), mEl=document.getElementById('cd-m'), sEl=document.getElementById('cd-s');
        if(hEl) hEl.textContent=('0'+h).slice(-2);
        if(mEl) mEl.textContent=('0'+m).slice(-2);
        if(sEl) sEl.textContent=('0'+s).slice(-2);
    }
    tick();
    setInterval(tick,1000);
})();

// Flying cart animation
document.querySelectorAll('.add-to-cart-form').forEach(function(form){
    form.addEventListener('submit',function(e){
        e.preventDefault();
        var card=this.closest('.prod-card'), img=card?card.querySelector('.prod-img'):null;
        var cart=document.getElementById('cart-nav-btn');
        if(img&&cart){
            var ir=img.getBoundingClientRect(), cr=cart.getBoundingClientRect();
            var fly=document.createElement('div'); fly.className='flying-item';
            fly.innerHTML='<i class="fas fa-shopping-cart"></i>';
            fly.style.cssText='left:'+ir.left+'px;top:'+ir.top+'px;';
            fly.style.setProperty('--tx',(cr.left-ir.left)+'px');
            fly.style.setProperty('--ty',(cr.top-ir.top)+'px');
            document.body.appendChild(fly);
            for(var i=0;i<6;i++){
                (function(){
                    var p=document.createElement('div'); p.className='particle';
                    var a=(i/6)*Math.PI*2;
                    p.style.cssText='left:'+(ir.left+ir.width/2)+'px;top:'+(ir.top+ir.height/2)+'px;width:7px;height:7px;';
                    p.style.setProperty('--px',(Math.cos(a)*60)+'px');
                    p.style.setProperty('--py',(Math.sin(a)*60)+'px');
                    document.body.appendChild(p);
                    setTimeout(function(){p.remove();},800);
                })();
            }
            setTimeout(function(){fly.remove();},800);
        }
        var fd=new FormData(this), url=this.action;
        fetch(url,{method:'POST',body:fd,headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
        .then(function(r){return r.json();}).then(function(d){
            if(d.success){updateCartBadge(d.cart_count||0);showNotif('✓ Added to cart!','success');}
            else{showNotif(d.message||'An error occurred','error');}
        }).catch(function(){showNotif('An error occurred','error');});
    });
});

function updateCartBadge(n){
    var btn=document.getElementById('cart-nav-btn'); if(!btn)return;
    var b=btn.querySelector('.cart-badge');
    if(n>0){if(!b){b=document.createElement('span');b.className='cart-badge';b.id='cart-badge';btn.appendChild(b);}b.textContent=n;}
    else if(b){b.remove();}
}
function showNotif(msg,type){
    var n=document.createElement('div');
    n.style.cssText='position:fixed;top:80px;right:20px;background:'+(type==='success'?'#00b894':'#e84040')+';color:white;padding:14px 20px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.2);z-index:9999;font-size:14px;font-weight:600;animation:slideIn .3s ease-out;max-width:280px';
    n.textContent=msg; document.body.appendChild(n);
    setTimeout(function(){n.style.animation='slideOut .3s ease-out';setTimeout(function(){n.remove();},300);},3000);
}

// Compare
<?php if(auth()->guard()->check()): ?>
var _csrf=document.querySelector('meta[name=csrf-token]').content;
function toggleCompare(id,btn){
    fetch('/compare/toggle/'+id,{method:'POST',headers:{'X-CSRF-TOKEN':_csrf,'Content-Type':'application/json'}})
    .then(function(r){return r.json();}).then(function(d){
        if(!d.ok){alert(d.message||'Cannot add');return;}
        if(d.in_compare){btn.style.cssText='background:#4f7df3;color:#fff;border-color:#4f7df3';btn.innerHTML='<i class="fas fa-check"></i> Added';}
        else{btn.style.cssText='background:#f0f5ff;color:#4f7df3;border-color:#c7d7fc';btn.innerHTML='<i class="fas fa-balance-scale"></i> Compare';}
        updateCompareBar(d.count);
    }).catch(function(){});
}
function updateCompareBar(c){
    var bar=document.getElementById('compare-bar'); if(!bar)return;
    bar.style.display=c>0?'flex':'none';
    document.getElementById('compare-count').textContent=c;
}
function clearCompare(){
    fetch('/compare/clear',{method:'POST',headers:{'X-CSRF-TOKEN':_csrf}})
    .then(function(){
        updateCompareBar(0);
        document.querySelectorAll('[data-id]').forEach(function(b){b.style.cssText='background:#f0f5ff;color:#4f7df3;border-color:#c7d7fc';b.innerHTML='<i class="fas fa-balance-scale"></i> Compare';});
    }).catch(function(){});
}
fetch('/compare/count').then(function(r){return r.json();}).then(function(d){if(d.count>0)updateCompareBar(d.count);}).catch(function(){});
<?php endif; ?>
</script>

<?php echo $__env->make('components.chatbot-widget', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
// ── Hamburger / Mobile Drawer ──────────────────────────────
(function(){
    var btn     = document.getElementById('hamburgerBtn');
    var drawer  = document.getElementById('mobileDrawer');
    var overlay = document.getElementById('drawerOverlay');
    var close   = document.getElementById('drawerClose');
    if(!btn) return;
    function openDrawer(){
        drawer.classList.add('open');
        btn.classList.add('active');
        document.body.style.overflow='hidden';
    }
    function closeDrawer(){
        drawer.classList.remove('open');
        btn.classList.remove('active');
        document.body.style.overflow='';
    }
    btn.addEventListener('click', openDrawer);
    close.addEventListener('click', closeDrawer);
    overlay.addEventListener('click', closeDrawer);
    // close on ESC
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeDrawer(); });
})();
</script>
</body>
</html>
<?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/home.blade.php ENDPATH**/ ?>