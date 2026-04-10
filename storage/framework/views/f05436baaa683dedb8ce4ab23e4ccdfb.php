
<?php $__env->startSection('title', 'Store Locations — TechStore'); ?>
<?php $__env->startSection('page_title', 'Store Locations'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .addr-wrap { max-width:1100px; margin:0 auto; padding:36px 24px 56px; }
    .addr-grid { display:grid; grid-template-columns:1fr 340px; gap:24px; align-items:start; }
    .addr-map { background:white; border-radius:14px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,.07); border:1.5px solid #e8edf2; }
    .addr-map iframe { display:block; }
    .addr-info { display:flex; flex-direction:column; gap:14px; }
    .ai-hdr { background:linear-gradient(135deg,#0b1a2e,#1a3a5c); border-radius:14px; padding:22px; color:white; }
    .ai-hdr h3 { font-size:16px; font-weight:800; margin:0 0 4px; }
    .ai-hdr p { font-size:12px; color:rgba(255,255,255,.5); margin:0; }
    .ai-card { background:white; border-radius:14px; padding:18px 20px; box-shadow:0 2px 10px rgba(0,0,0,.07); border:1.5px solid #e8edf2; display:flex; align-items:flex-start; gap:14px; transition:all .25s; }
    .ai-card:hover { border-color:#00b894; }
    .ai-ico { width:40px; height:40px; background:#e6f7f4; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .ai-ico i { color:#00b894; font-size:16px; }
    .ai-lbl { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#94a3b8; margin-bottom:4px; }
    .ai-val { font-size:13px; color:#1a1f2e; font-weight:600; line-height:1.65; }
    .ai-val a { color:#0984e3; text-decoration:none; }
    .ai-val a:hover { text-decoration:underline; }
    @media(max-width:860px){.addr-grid{grid-template-columns:1fr;}}
</style>
<div class="addr-wrap">
    <div class="addr-grid">
        <div class="addr-map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.5578840160236!2d105.80432097550449!3d21.003971888373826!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ac6e8f8f8f8f%3A0x1f8f8f8f8f8f8f8f!2z4biz!5e0!3m2!1svi!2s!4v1234567890" width="100%" height="460" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="addr-info">
            <div class="ai-hdr">
                <h3>📍 TechStore Can Tho</h3>
                <p>Genuine Tech Accessories Store</p>
            </div>
            <div class="ai-card"><div class="ai-ico"><i class="fas fa-map-marker-alt"></i></div><div><div class="ai-lbl">Address</div><div class="ai-val">Hem 1, Pham Ngu Lao St<br>An Hoa Ward, Ninh Kieu District<br>Can Tho</div></div></div>
            <div class="ai-card"><div class="ai-ico"><i class="fas fa-phone-alt"></i></div><div><div class="ai-lbl">Phone</div><div class="ai-val"><a href="tel:19001234">1900-1234</a><br><a href="tel:02412345678">024-1234-5678</a></div></div></div>
            <div class="ai-card"><div class="ai-ico"><i class="fas fa-envelope"></i></div><div><div class="ai-lbl">Email</div><div class="ai-val"><a href="mailto:support@techstore.com">support@techstore.com</a></div></div></div>
            <div class="ai-card"><div class="ai-ico"><i class="fas fa-clock"></i></div><div><div class="ai-lbl">Business Hours</div><div class="ai-val">Mon – Fri: 08:00 – 18:00<br>Sat: 09:00 – 17:00<br>Sun: 10:00 – 16:00</div></div></div>
            <div class="ai-card"><div class="ai-ico"><i class="fas fa-directions"></i></div><div><div class="ai-lbl">Directions</div><div class="ai-val">Near Cau Giay Metro station. Note one-way traffic.</div></div></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\pages\address.blade.php ENDPATH**/ ?>