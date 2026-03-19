<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — TechStore Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
    --sb-w:240px;--tb-h:56px;
    --sb:#0f172a;--sb2:#1e293b;
    --accent:#3b82f6;--accent-d:#2563eb;
    --success:#10b981;--danger:#ef4444;--warning:#f59e0b;--info:#06b6d4;
    --bg:#f1f5f9;--card:#fff;--border:#e2e8f0;
    --text:#0f172a;--muted:#64748b;
    --r:10px;--shadow:0 1px 3px rgba(0,0,0,.08);--shadow-md:0 4px 16px rgba(0,0,0,.1);
}
html,body{height:100vh;overflow:hidden}
body{font-family:'Inter',sans-serif;font-size:14px;background:var(--bg);color:var(--text)}

/* ── Shell ── */
.admin-shell{display:flex;height:100vh}

/* ── Sidebar ── */
.admin-sidebar{width:var(--sb-w);background:var(--sb);display:flex;flex-direction:column;flex-shrink:0;z-index:100;transition:transform .25s;overflow:hidden}
.sb-brand{display:flex;align-items:center;gap:10px;padding:16px 14px 13px;border-bottom:1px solid rgba(255,255,255,.07);text-decoration:none;flex-shrink:0}
.sb-brand .bi{width:32px;height:32px;background:linear-gradient(135deg,var(--accent),var(--info));border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;color:#fff;flex-shrink:0}
.sb-brand .bn{font-weight:700;font-size:15px;color:#fff;letter-spacing:-.3px}
.sb-brand .bp{margin-left:auto;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;background:rgba(59,130,246,.2);color:#93c5fd;padding:2px 7px;border-radius:999px}
.sb-nav{flex:1;overflow-y:auto;padding:8px 0 4px}
.sb-nav::-webkit-scrollbar{width:0}
.sb-group{margin-bottom:2px}
.sb-label{font-size:10px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:rgba(255,255,255,.22);padding:10px 14px 3px}
.nav-item{display:flex;align-items:center;gap:9px;padding:8px 14px;color:rgba(255,255,255,.52);text-decoration:none;font-size:13.5px;font-weight:400;border-left:2px solid transparent;transition:background .15s,color .15s,border-color .15s;cursor:pointer;white-space:nowrap;user-select:none}
.nav-item:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.82)}
.nav-item.active{background:rgba(59,130,246,.13);color:#93c5fd;border-left-color:var(--accent);font-weight:500}
.nav-item .ni{width:15px;text-align:center;font-size:12.5px;flex-shrink:0;opacity:.75}
.nav-item.active .ni{opacity:1}
.sb-bottom{border-top:1px solid rgba(255,255,255,.07);padding:8px;flex-shrink:0}
.sb-user{display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:8px;transition:background .15s}
.sb-user:hover{background:rgba(255,255,255,.06)}
.sb-user .av{width:30px;height:30px;background:linear-gradient(135deg,var(--accent),#7c3aed);border-radius:7px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#fff;flex-shrink:0}
.sb-user .ui{flex:1;min-width:0}
.sb-user .un{font-size:12.5px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sb-user .ur{font-size:10.5px;color:rgba(255,255,255,.28)}
.sb-out{background:none;border:none;cursor:pointer;color:rgba(255,255,255,.25);padding:4px;border-radius:4px;font-size:13px;transition:color .15s,background .15s}
.sb-out:hover{color:var(--danger);background:rgba(239,68,68,.15)}

/* ── Main ── */
.admin-main{flex:1;display:flex;flex-direction:column;min-width:0;overflow:visible}

/* ── Topbar ── */
.admin-topbar{height:var(--tb-h);background:var(--card);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 20px;gap:10px;flex-shrink:0;box-shadow:var(--shadow);z-index:50}
.tb-title{flex:1;min-width:0}
.tb-title h1{font-size:15px;font-weight:600;color:var(--text);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tb-actions{display:flex;align-items:center;gap:5px}
.tb-clock{font-size:12px;color:var(--muted);font-variant-numeric:tabular-nums;padding:0 4px}
.tb-btn{width:30px;height:30px;background:none;border:1px solid var(--border);border-radius:7px;display:flex;align-items:center;justify-content:center;color:var(--muted);cursor:pointer;transition:all .15s;font-size:12.5px;text-decoration:none}
.tb-btn:hover{background:var(--bg);color:var(--text)}
.sb-toggle{display:none}
@media(max-width:768px){.sb-toggle{display:flex}}
.tb-user{display:flex;align-items:center;gap:5px;cursor:pointer;position:relative;padding:3px 7px;border-radius:7px;transition:background .15s}
.tb-user:hover{background:var(--bg)}
.tb-user .av{width:26px;height:26px;background:linear-gradient(135deg,var(--accent),#7c3aed);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff}
.tb-user .un{font-size:13px;font-weight:500;color:var(--text)}
.tb-drop{position:absolute;top:100%;right:0;padding-top:6px;background:transparent;border:none;box-shadow:none;min-width:165px;z-index:999;display:none}
.tb-drop-inner{background:#fff;border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--shadow-md);overflow:hidden}
.tb-user:hover .tb-drop{display:block}
.tb-drop-inner a{display:flex;align-items:center;gap:8px;padding:9px 14px;color:var(--text);text-decoration:none;font-size:13px;transition:background .15s;border-bottom:1px solid #f8fafc}
.tb-drop-inner a:last-child{border:none}
.tb-drop-inner a:hover{background:var(--bg)}
.tb-drop-inner a.del{color:var(--danger)}
.tb-drop-inner a.del:hover{background:#fef2f2}

/* ── Content ── */
#admin-content-area{flex:1;overflow-y:auto;padding:20px}
#admin-content-area::-webkit-scrollbar{width:5px}
#admin-content-area::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px}

/* ── Card ── */
.card{background:var(--card);border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--shadow)}
.card-header{background:transparent;border-bottom:1px solid var(--border);padding:13px 18px}
.card-header h5,.card-header h6{font-size:14px;font-weight:600;color:var(--text);margin:0}
.card-body{padding:18px}
.card-footer{background:transparent;border-top:1px solid var(--border);padding:12px 18px}

/* ── Stat cards ── */
.stat-card{background:var(--card);border:1px solid var(--border);border-radius:var(--r);padding:16px 18px;display:flex;align-items:center;gap:13px;box-shadow:var(--shadow);height:100%}
.stat-icon{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.stat-val{font-size:20px;font-weight:700;color:var(--text);line-height:1.2}
.stat-lbl{font-size:12px;color:var(--muted);margin-top:2px}

/* ── Table ── */
.table{font-size:13.5px;color:var(--text);margin:0}
.table thead th{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);background:#f8fafc;border-bottom:1px solid var(--border);padding:10px 16px;white-space:nowrap;border-top:none}
.table tbody td{padding:11px 16px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
.table tbody tr:last-child td{border-bottom:none}
.table-hover tbody tr:hover td{background:#fafbfc}

/* ── Badges ── */
.bs{padding:3px 9px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;white-space:nowrap}
.bs-pending{background:#fef3c7;color:#92400e}
.bs-processing{background:#dbeafe;color:#1e40af}
.bs-shipped{background:#ede9fe;color:#6d28d9}
.bs-delivered{background:#d1fae5;color:#065f46}
.bs-cancelled{background:#fee2e2;color:#991b1b}
.bs-active{background:#d1fae5;color:#065f46}
.bs-inactive,.bs-blocked{background:#fee2e2;color:#991b1b}
.bs-admin{background:#dbeafe;color:#1e40af}
.bs-user{background:#f3f4f6;color:#374151}
.bs-low{background:#d1fae5;color:#065f46}
.bs-medium{background:#fef3c7;color:#92400e}
.bs-high{background:#fee2e2;color:#991b1b}
.bs-critical{background:#fce7f3;color:#9d174d}
.bs-stock-ok{background:#d1fae5;color:#065f46}
.bs-stock-out{background:#fee2e2;color:#991b1b}

/* ── Buttons ── */
.btn{border-radius:7px;font-size:13px;font-weight:500}
.btn-primary{background:var(--accent);border-color:var(--accent)}
.btn-primary:hover{background:var(--accent-d);border-color:var(--accent-d)}
.btn-sm{font-size:12px;padding:4px 11px}
.btn-icon{width:28px;height:28px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--border);background:#fff;color:var(--muted);font-size:12px;transition:all .15s;cursor:pointer;text-decoration:none;vertical-align:middle}
.btn-icon:hover{background:var(--bg);color:var(--text)}
.btn-icon-danger{color:var(--danger)}
.btn-icon-danger:hover{background:#fee2e2;border-color:#fca5a5;color:var(--danger)}
.btn-icon-primary{color:var(--accent)}
.btn-icon-primary:hover{background:#dbeafe;border-color:#93c5fd;color:var(--accent)}

/* ── Form ── */
.form-control,.form-select{font-size:13.5px;border-radius:7px;border:1px solid var(--border);padding:7px 12px}
.form-control:focus,.form-select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(59,130,246,.1)}
.form-label{font-size:13px;font-weight:500;color:var(--text);margin-bottom:5px}

/* ── Alert ── */
.alert{border-radius:8px;border:none;font-size:13.5px}
.alert-success{background:#d1fae5;color:#065f46}
.alert-danger{background:#fee2e2;color:#991b1b}
.alert-warning{background:#fef3c7;color:#92400e}
.alert-info{background:#dbeafe;color:#1e40af}

/* ── Page header ── */
.pg-hdr{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:18px;gap:12px}
.pg-hdr h2{font-size:17px;font-weight:700;color:var(--text);margin:0}
.pg-hdr p{font-size:12px;color:var(--muted);margin:3px 0 0}

/* ── Progress ── */
#nav-prog{position:fixed;top:0;left:0;height:2px;background:linear-gradient(90deg,var(--accent),var(--info));width:0;z-index:9999;pointer-events:none;transition:width .35s ease,opacity .25s}

/* ── Loading ── */
.pg-load{display:flex;align-items:center;justify-content:center;padding:80px 0;color:var(--muted)}
.pg-load .sp{width:24px;height:24px;border:2.5px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .65s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

/* ── Empty ── */
.empty-state{padding:48px 24px;text-align:center;color:var(--muted)}
.empty-state i{font-size:34px;opacity:.3;margin-bottom:12px;display:block}
.empty-state p{margin:0;font-size:13.5px}

/* ── Mobile ── */
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:99}
@media(max-width:768px){
    .admin-sidebar{position:fixed;height:100%;transform:translateX(-100%);z-index:150}
    .admin-sidebar.open{transform:translateX(0)}
    .sb-overlay.open{display:block}
    #admin-content-area{padding:14px}
    .admin-topbar{padding:0 12px}
}
</style>
@yield('extra-css')
</head>
<body>
<div id="nav-prog"></div>
<div class="admin-shell">
    @include('admin.partials.sidebar')
    <div class="admin-main">
        @include('admin.partials.topbar')
        <div id="admin-content-area">
            @if(session('face_enroll_prompt'))
                <div class="alert alert-warning alert-dismissible fade show mx-3 mt-3" role="alert">
                    <i class="fas fa-camera me-1"></i>
                    <strong>Face profile not set up.</strong>
                    To enable biometric (face) 3FA verification,
                    <a href="{{ route('auth.face.enroll.form') }}" class="alert-link fw-bold">enroll your face here</a>.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('body-content')
        </div>
    </div>
</div>
<div class="sb-overlay" id="sb-overlay"></div>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('extra-js')
<script>
(function(){
    const prog = document.getElementById('nav-prog');
    const area = document.getElementById('admin-content-area');
    const sbEl = document.querySelector('.admin-sidebar');
    const ov   = document.getElementById('sb-overlay');
    let pt;

    function startProg(){
        clearTimeout(pt);
        prog.style.transition='none'; prog.style.width='0'; prog.style.opacity='1';
        requestAnimationFrame(()=>{
            prog.style.transition='width .4s ease'; prog.style.width='65%';
        });
    }
    function endProg(){
        prog.style.width='100%';
        pt=setTimeout(()=>{ prog.style.opacity='0'; setTimeout(()=>{ prog.style.width='0'; prog.style.opacity='1'; },220); },300);
    }
    function setActive(url){
        const p=new URL(url,location.origin).pathname;
        document.querySelectorAll('.nav-item[data-nav]').forEach(el=>{
            const np=el.getAttribute('data-nav');
            const exact=el.dataset.navExact==='1';
            el.classList.toggle('active', exact ? p===np : (p===np || p.startsWith(np+'/')));
        });
    }
    function reinit(c){
        c.querySelectorAll('script').forEach(old=>{
            const s=document.createElement('script');
            [...old.attributes].forEach(a=>s.setAttribute(a.name,a.value));
            s.textContent=old.textContent;
            old.parentNode.replaceChild(s,old);
        });
    }
    async function goTo(url,push=true){
        startProg();
        area.innerHTML='<div class="pg-load"><div class="sp"></div></div>';
        if(push) history.pushState({url},'' ,url);
        setActive(url);
        try{
            const res=await fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest'}});
            if(!res.ok) throw 0;
            const html=await res.text();
            const doc=new DOMParser().parseFromString(html,'text/html');
            const nc=doc.getElementById('admin-content-area');
            if(nc){ area.innerHTML=nc.innerHTML; reinit(area); }
            const nt=doc.getElementById('tb-page-title');
            const ct=document.getElementById('tb-page-title');
            if(nt&&ct) ct.textContent=nt.textContent;
            document.title=doc.title;
        }catch{ location.href=url; }
        endProg();
    }
    document.querySelectorAll('.nav-item[data-nav]').forEach(el=>{
        el.addEventListener('click',e=>{
            e.preventDefault();
            const url=el.getAttribute('href');
            if(url){ goTo(url); sbEl.classList.remove('open'); ov.classList.remove('open'); }
        });
    });
    window.addEventListener('popstate',e=>{ if(e.state?.url) goTo(e.state.url,false); });
    const tog=document.getElementById('sb-toggle');
    if(tog) tog.addEventListener('click',()=>{ sbEl.classList.toggle('open'); ov.classList.toggle('open'); });
    ov.addEventListener('click',()=>{ sbEl.classList.remove('open'); ov.classList.remove('open'); });
    setActive(location.href);
    function tick(){ const el=document.getElementById('tb-clock'); if(el) el.textContent=new Date().toLocaleTimeString('vi-VN',{hour:'2-digit',minute:'2-digit'}); }
    tick(); setInterval(tick,60000);
})();
</script>
</body>
</html>