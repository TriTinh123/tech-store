
<?php $__env->startSection('page_title', $product->name); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
<style>
.pd-img-wrap { background:#f4f7fa;border-radius:16px;overflow:hidden;display:flex;align-items:center;justify-content:center;min-height:380px;position:relative; }
.pd-img-wrap img { max-width:100%;max-height:380px;object-fit:contain; }
.pd-badge-discount { position:absolute;top:14px;right:14px;background:#e84040;color:#fff;padding:5px 12px;border-radius:20px;font-size:13px;font-weight:700; }
.pd-rating i { color:#f59e0b;font-size:15px; }
.pd-price-current { font-size:32px;font-weight:800;color:#e84040;line-height:1; }
.pd-price-original { font-size:17px;text-decoration:line-through;color:#94a3b8; }
.stock-badge-in { background:#d1fae5;color:#065f46;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600; }
.stock-badge-out { background:#fee2e2;color:#991b1b;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600; }
.qty-btn { width:38px;height:38px;border:1.5px solid var(--border);background:#fff;border-radius:8px;cursor:pointer;font-size:18px;transition:.15s; }
.qty-btn:hover { background:var(--green);border-color:var(--green);color:#fff; }
.qty-in { width:52px;text-align:center;border:1.5px solid var(--border);border-radius:8px;padding:6px 0;font-size:15px;font-weight:600; }
.btn-add-cart { flex:1;padding:11px 18px;background:linear-gradient(135deg,#00b894,#00cec9);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;transition:.2s; }
.btn-add-cart:hover { opacity:.88; }
.btn-add-cart:disabled { background:#cbd5e0;cursor:not-allowed; }
.feature-check li { padding:5px 0;font-size:14px; }
.feature-check li i { color:var(--green);margin-right:8px; }
.section-head { font-size:18px;font-weight:700;color:var(--text);padding-bottom:10px;border-bottom:2px solid var(--green);margin-bottom:20px; }
.review-card { background:#f8fafc;border-radius:12px;padding:18px;margin-bottom:14px; }
.star-filled { color:#f59e0b; }
.star-empty { color:#e2e8f0; }
.related-card { background:#fff;border-radius:14px;border:1px solid var(--border);overflow:hidden;transition:.2s; }
.related-card:hover { transform:translateY(-4px);box-shadow:0 10px 30px rgba(0,0,0,.12); }
.related-card img { width:100%;height:140px;object-fit:contain;background:#f4f7fa;padding:10px; }
.breadcrumb-item a { color:var(--green);text-decoration:none; }
.breadcrumb-item a:hover { text-decoration:underline; }
</style>


<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(url('/?category='.$product->category)); ?>"><?php echo e(ucfirst($product->category)); ?></a></li>
        <li class="breadcrumb-item active"><?php echo e(Str::limit($product->name, 50)); ?></li>
    </ol>
</nav>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
    <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>


<div class="bg-white rounded-4 border p-4 mb-4" style="border-color:var(--border)!important">
    <div class="row g-4">
        
        <div class="col-md-5">
            <div class="pd-img-wrap">
                <?php if(($product->discount_percentage ?? 0) > 0): ?>
                    <span class="pd-badge-discount">-<?php echo e($product->discount_percentage); ?>%</span>
                <?php endif; ?>
                <img src="<?php echo e($product->image ?? asset('images/no-image.svg')); ?>" alt="<?php echo e($product->name); ?>">
            </div>
        </div>

        
        <div class="col-md-7">
            <h1 style="font-size:22px;font-weight:800;color:var(--text);line-height:1.35"><?php echo e($product->name); ?></h1>

            <div class="d-flex align-items-center gap-2 my-2 pd-rating">
                <?php for($i=0;$i<5;$i++): ?>
                    <i class="<?php echo e($i < ($product->rating??0) ? 'fas' : 'far'); ?> fa-star"></i>
                <?php endfor; ?>
                <span style="font-size:13px;color:var(--text-m)">(<?php echo e($product->reviews_count ?? 0); ?> reviews)</span>
            </div>

            <div class="d-flex align-items-baseline gap-3 mb-3">
                <?php if($flashSalePrice): ?>
                    <span class="pd-price-current">$<?php echo e(number_format($flashSalePrice, 2)); ?></span>
                    <span class="pd-price-original">$<?php echo e(number_format($product->price, 2)); ?></span>
                    <span style="background:linear-gradient(135deg,#e84040,#c0392b);color:#fff;font-size:12px;font-weight:700;padding:3px 10px;border-radius:50px">⚡ -<?php echo e($flashSaleDiscount); ?>% TODAY</span>
                <?php else: ?>
                    <span class="pd-price-current">$<?php echo e(number_format($product->price, 2)); ?></span>
                    <?php if($product->original_price): ?>
                        <span class="pd-price-original">$<?php echo e(number_format($product->original_price, 2)); ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <?php if(($product->stock??0)>0): ?>
                    <span class="stock-badge-in"><i class="fas fa-check-circle me-1"></i>In Stock (<?php echo e($product->stock); ?> products)</span>
                <?php else: ?>
                    <span class="stock-badge-out"><i class="fas fa-times-circle me-1"></i>Out of Stock</span>
                <?php endif; ?>
            </div>

            <form action="<?php echo e(route('cart.add', $product->id)); ?>" method="POST" class="d-flex align-items-center gap-2 mb-3">
                <?php echo csrf_field(); ?>
                <button type="button" class="qty-btn" onclick="pdStep(-1)">−</button>
                <input type="number" id="pdQty" name="quantity" value="1" min="1" class="qty-in" readonly>
                <button type="button" class="qty-btn" onclick="pdStep(1)">+</button>
                <button type="submit" class="btn-add-cart" <?php if(($product->stock??0)<=0): ?> disabled <?php endif; ?>>
                    <i class="fas fa-cart-plus me-2"></i>Add to cart
                </button>
            </form>

            <?php if(auth()->guard()->check()): ?>
            <?php $isWished = \App\Models\Wishlist::where('user_id',auth()->id())->where('product_id',$product->id)->exists(); ?>
            <button id="wish-btn" onclick="toggleWish(<?php echo e($product->id); ?>)" class="btn d-flex align-items-center gap-2 mb-3"
                style="border:2px solid <?php echo e($isWished?'#e74c3c':'var(--border)'); ?>;background:<?php echo e($isWished?'#fee2e2':'#fff'); ?>;color:<?php echo e($isWished?'#e74c3c':'var(--text-m)'); ?>;border-radius:10px;padding:9px 18px;font-size:14px;font-weight:600;transition:.2s">
                <i class="fas fa-heart" id="wish-icon"></i>
                <span id="wish-label"><?php echo e($isWished?'Wishlisted':'Add to wishlist'); ?></span>
            </button>
            <?php endif; ?>

            <div class="p-3 rounded-3" style="background:#f4f7fa">
                <div class="fw-700 mb-2" style="font-size:14px"><i class="fas fa-star me-2 text-warning"></i>Highlights</div>
                <ul class="list-unstyled feature-check mb-0">
                    <li><i class="fas fa-check"></i>100% Genuine</li>
                    <li><i class="fas fa-check"></i>2-year full warranty</li>
                    <li><i class="fas fa-check"></i>Free shipping nationwide</li>
                    <li><i class="fas fa-check"></i>Customer Support 24/7</li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="bg-white rounded-4 border p-4 mb-4" style="border-color:var(--border)!important">
    <div class="section-head"><i class="fas fa-align-left me-2 text-success"></i>Product Description</div>
    <div style="line-height:1.9;color:var(--text)"><?php echo nl2br(e($product->description ?? 'No description available.')); ?></div>
</div>


<div class="bg-white rounded-4 border p-4 mb-4" style="border-color:var(--border)!important">
    <div class="section-head"><i class="fas fa-star me-2 text-warning"></i>Rating products</div>

    <?php if(count($product->reviews??[])>0): ?>
        <?php $__currentLoopData = $product->reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="review-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="fw-700"><?php echo e($review->user_name ?? 'Anonymous'); ?></span>
                        <div class="mt-1">
                            <?php for($i=0;$i<5;$i++): ?>
                                <i class="<?php echo e($i<($review->rating??0)?'fas':'far'); ?> fa-star <?php echo e($i<($review->rating??0)?'star-filled':'star-empty'); ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <span class="text-muted" style="font-size:12px"><?php echo e($review->created_at->format('d/m/Y H:i')); ?></span>
                </div>
                <p class="mt-2 mb-0" style="font-size:14px"><?php echo e($review->comment); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="text-center py-4 text-muted"><i class="far fa-comment fa-2x mb-2 d-block"></i>No reviews yet. Be the first!</div>
    <?php endif; ?>

    
    <div class="mt-4 p-4 rounded-3" style="background:#f8fafc;border:1px solid var(--border)">
        <h6 class="fw-700 mb-3"><i class="fas fa-pen me-2 text-success"></i>Write your review</h6>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger rounded-3 py-2 mb-3">
                <ul class="mb-0 ps-3"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li style="font-size:13px"><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
            </div>
        <?php endif; ?>
        <form action="<?php echo e(route('product.review', $product->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label fw-600" style="font-size:13px">Your name</label>
                    <input type="text" name="user_name" value="<?php echo e(old('user_name', auth()->user()->name ?? '')); ?>" class="form-control form-control-sm" required>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-600" style="font-size:13px">Email</label>
                    <input type="email" name="user_email" value="<?php echo e(old('user_email', auth()->user()->email ?? '')); ?>" class="form-control form-control-sm" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600" style="font-size:13px">Rating</label>
                <div id="star-rating" class="d-flex gap-2 mb-1" style="font-size:28px;cursor:pointer">
                    <?php for($i=1;$i<=5;$i++): ?>
                        <i class="<?php echo e(old('rating',0)>=$i?'fas':'far'); ?> fa-star star-btn" data-val="<?php echo e($i); ?>" style="color:#f59e0b;transition:transform .1s" onclick="setRating(<?php echo e($i); ?>)" onmouseenter="hoverRating(<?php echo e($i); ?>)" onmouseleave="resetRating()"></i>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="rating" id="rating-val" value="<?php echo e(old('rating','')); ?>" required>
                <div id="rating-label" style="font-size:12px;color:var(--text-m);min-height:18px"></div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600" style="font-size:13px">Comment</label>
                <textarea name="comment" class="form-control" rows="3" placeholder="Share your thoughts..." required><?php echo e(old('comment')); ?></textarea>
            </div>
            <button type="submit" class="btn btn-success rounded-pill px-4 fw-600">
                <i class="fas fa-paper-plane me-2"></i>Send reviews
            </button>
        </form>
    </div>
</div>


<?php if(count($relatedProducts??[])>0): ?>
<div class="mb-4">
    <h5 class="fw-700 mb-3"><i class="fas fa-th-large me-2 text-success"></i>Related Products</h5>
    <div class="row g-3">
        <?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-6 col-md-3">
            <div class="related-card h-100">
                <a href="<?php echo e(route('product.show', $rp->id)); ?>">
                    <img src="<?php echo e($rp->image ?? asset('images/no-image.svg')); ?>" alt="<?php echo e($rp->name); ?>">
                </a>
                <div class="p-3">
                    <a href="<?php echo e(route('product.show', $rp->id)); ?>" class="d-block fw-600 text-decoration-none mb-1" style="font-size:13px;color:var(--text);line-height:1.4;min-height:36px"><?php echo e(Str::limit($rp->name,50)); ?></a>
                    <div style="color:#e84040;font-weight:700;font-size:14px;margin-bottom:8px">$<?php echo e(number_format($rp->price, 2)); ?></div>
                    <form action="<?php echo e(route('cart.add', $rp->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill fw-600" style="font-size:12px">
                            <i class="fas fa-cart-plus me-1"></i>Add to cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>
</div>

<script>
function pdStep(d) {
    const i = document.getElementById('pdQty');
    i.value = Math.max(1, parseInt(i.value)+d);
}
var selectedRating = <?php echo e(old('rating',0)); ?>;
var ratingLabels = ['','⭐ Very bad','⭐⭐ Not good','⭐⭐⭐ Average','⭐⭐⭐⭐ Good','⭐⭐⭐⭐⭐ Excellent'];
function setRating(v) { selectedRating=v; document.getElementById('rating-val').value=v; document.getElementById('rating-label').textContent=ratingLabels[v]; renderStars(v); }
function hoverRating(v) { renderStars(v); document.getElementById('rating-label').textContent=ratingLabels[v]; }
function resetRating() { renderStars(selectedRating); document.getElementById('rating-label').textContent=selectedRating?ratingLabels[selectedRating]:''; }
function renderStars(v) { document.querySelectorAll('.star-btn').forEach(function(s,i){ s.className=(i<v?'fas':'far')+' fa-star star-btn'; s.style.transform=i<v?'scale(1.15)':'scale(1)'; }); }
if(selectedRating>0){renderStars(selectedRating);document.getElementById('rating-label').textContent=ratingLabels[selectedRating];}
<?php if(auth()->guard()->check()): ?>
function toggleWish(pid) {
    fetch(`/wishlist/toggle/${pid}`,{method:'POST',headers:{'X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>','Content-Type':'application/json'}})
    .then(r=>r.json()).then(data=>{
        if(!data.ok)return;
        const btn=document.getElementById('wish-btn');
        const lbl=document.getElementById('wish-label');
        if(data.liked){btn.style.borderColor='#e74c3c';btn.style.background='#fee2e2';btn.style.color='#e74c3c';lbl.textContent='Wishlisted';}
        else{btn.style.borderColor='var(--border)';btn.style.background='#fff';btn.style.color='var(--text-m)';lbl.textContent='Add to wishlist';}
    });
}
<?php endif; ?>
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/product-detail.blade.php ENDPATH**/ ?>