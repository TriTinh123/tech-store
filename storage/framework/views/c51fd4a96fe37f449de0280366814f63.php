
<?php $__env->startSection('title', 'Tech News — TechStore'); ?>
<?php $__env->startSection('page_title', 'Tech News'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .news-wrap { max-width:1100px; margin:0 auto; padding:36px 24px 56px; }
    .news-sec-hdr { display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:28px; }
    .news-sec-title { font-size:22px; font-weight:800; color:#1a1f2e; position:relative; padding-left:14px; }
    .news-sec-title::before { content:''; position:absolute; left:0; top:2px; bottom:2px; width:4px; background:linear-gradient(180deg,#00b894,#0984e3); border-radius:2px; }
    .pg-news-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
    .pg-nc { background:white; border-radius:14px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,.07); border:1.5px solid #e8edf2; transition:all .3s; text-decoration:none; color:inherit; display:block; }
    .pg-nc:hover { box-shadow:0 10px 30px rgba(0,0,0,.12); transform:translateY(-6px); border-color:#00b894; }
    .pg-nc-img-wrap { overflow:hidden; height:185px; }
    .pg-nc-img { width:100%; height:185px; object-fit:cover; transition:transform .4s; }
    .pg-nc:hover .pg-nc-img { transform:scale(1.07); }
    .pg-nc-body { padding:18px; }
    .pg-nc-cat { display:inline-block; background:#e6f7f4; color:#00b894; font-size:10px; font-weight:700; text-transform:uppercase; padding:3px 10px; border-radius:50px; margin-bottom:10px; letter-spacing:.4px; }
    .pg-nc-ttl { font-size:14px; font-weight:700; color:#1a1f2e; line-height:1.5; margin-bottom:10px; }
    .pg-nc:hover .pg-nc-ttl { color:#00b894; }
    .pg-nc-desc { font-size:12px; color:#64748b; line-height:1.7; margin-bottom:12px; }
    .pg-nc-meta { font-size:11px; color:#94a3b8; display:flex; gap:14px; }
    .pg-nc-meta i { color:#00b894; }
    @media(max-width:900px){.pg-news-grid{grid-template-columns:repeat(2,1fr);}}
    @media(max-width:600px){.pg-news-grid{grid-template-columns:1fr;}}
</style>
<div class="news-wrap">
    <div class="news-sec-hdr">
        <div class="news-sec-title">📰 Latest Articles</div>
        <span style="font-size:12px;color:#64748b">Tracking tech trends 2026</span>
    </div>
    <div class="pg-news-grid">
        <a href="#" class="pg-nc">
            <div class="pg-nc-img-wrap"><img src="/images/sptt1.jpg" alt="Gaming" class="pg-nc-img"></div>
            <div class="pg-nc-body">
                <span class="pg-nc-cat">Gaming</span>
                <div class="pg-nc-ttl">Top 5 mechanical gaming keyboards in 2026 — Which one should you choose?</div>
                <p class="pg-nc-desc">Mechanical gaming keyboards are becoming increasingly popular thanks to their durability, RGB lighting, and responsive switches. In this article, we review the top 5 mechanical keyboards in 2026 that offer the best performance for gamers.</p>
                <div class="pg-nc-meta"><span><i class="fas fa-calendar-alt"></i> 03/02/2026</span><span><i class="fas fa-user"></i> TechStore</span></div>
            </div>
        </a>
        <a href="#" class="pg-nc">
            <div class="pg-nc-img-wrap"><img src="/images/sptt2.jpg" alt="Review" class="pg-nc-img"></div>
            <div class="pg-nc-body">
                <span class="pg-nc-cat">Review</span>
                <div class="pg-nc-ttl">Wireless vs wired gaming mouse — Which is better for gamers in 2026?</div>
                <p class="pg-nc-desc">Choosing between a wired and wireless gaming mouse can be difficult. This review compares latency, battery life, and performance to help gamers choose the best option for their gaming setup.</p>
                <div class="pg-nc-meta"><span><i class="fas fa-calendar-alt"></i> 02/02/2026</span><span><i class="fas fa-user"></i> TechStore</span></div>
            </div>
        </a>
        <a href="#" class="pg-nc">
            <div class="pg-nc-img-wrap"><img src="/images/sptt3.jpg" alt="Setup" class="pg-nc-img"></div>
            <div class="pg-nc-body">
                <span class="pg-nc-cat">Setup</span>
                <div class="pg-nc-ttl">Professional gaming headset — Best choices for streamers in 2026</div>
                <p class="pg-nc-desc">A good gaming headset is essential for clear communication and immersive gameplay. This guide introduces several professional headsets suitable for streaming, gaming, and online meetings.</p>
                <div class="pg-nc-meta"><span><i class="fas fa-calendar-alt"></i> 31/01/2026</span><span><i class="fas fa-user"></i> TechStore</span></div>
            </div>
        </a>
        <a href="#" class="pg-nc">
            <div class="pg-nc-img-wrap"><img src="/images/sptt4.jpg" alt="Technology" class="pg-nc-img"></div>
            <div class="pg-nc-body">
                <span class="pg-nc-cat">Technology</span>
                <div class="pg-nc-ttl">Latest gaming monitors in 2026 — High refresh rate and 4K performance</div>
                <p class="pg-nc-desc">Gaming monitors continue to evolve with higher refresh rates, better color accuracy, and faster response times. This article explores the latest monitor technologies designed for modern gamers.</p>
                <div class="pg-nc-meta"><span><i class="fas fa-calendar-alt"></i> 29/01/2026</span><span><i class="fas fa-user"></i> TechStore</span></div>
            </div>
        </a>
        <a href="#" class="pg-nc">
            <div class="pg-nc-img-wrap"><img src="/images/sptt1.jpg" alt="Tips" class="pg-nc-img"></div>
            <div class="pg-nc-body">
                <span class="pg-nc-cat">Tips &amp; Tricks</span>
                <div class="pg-nc-ttl">7 tips to speed up your PC with budget accessories — Instant results</div>
                <p class="pg-nc-desc">Simple but highly effective accessories to significantly boost work and gaming performance...</p>
                <div class="pg-nc-meta"><span><i class="fas fa-calendar-alt"></i> 27/01/2026</span><span><i class="fas fa-user"></i> TechStore</span></div>
            </div>
        </a>
        <a href="#" class="pg-nc">
            <div class="pg-nc-img-wrap"><img src="/images/sptt3.jpg" alt="Trends" class="pg-nc-img"></div>
            <div class="pg-nc-body">
                <span class="pg-nc-cat">Trends</span>
                <div class="pg-nc-ttl">Peripheral Device Trends 2026 — Which tech is dominating?</div>
                <p class="pg-nc-desc">A look back and predictions for the most prominent tech accessory trends in 2026 and beyond...</p>
                <div class="pg-nc-meta"><span><i class="fas fa-calendar-alt"></i> 25/01/2026</span><span><i class="fas fa-user"></i> TechStore</span></div>
            </div>
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\pages\news.blade.php ENDPATH**/ ?>