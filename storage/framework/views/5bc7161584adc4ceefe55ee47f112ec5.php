

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 style="font-weight:700;color:#1a202c"><i class="fas fa-heart" style="color:#e74c3c"></i> Wishlist</h2>
        <a href="<?php echo e(route('products.index')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Continue shopping
        </a>
    </div>

    <?php if($items->isEmpty()): ?>
    <div class="text-center py-5">
        <i class="fas fa-heart-broken" style="font-size:60px;color:#e2e8f0"></i>
        <p class="mt-3 text-muted">You have no items in your wishlist.</p>
        <a href="<?php echo e(route('products.index')); ?>" class="btn btn-primary mt-2">Explore products</a>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($item->product): ?>
        <div class="col-6 col-md-4 col-lg-3" id="wish-card-<?php echo e($item->product_id); ?>">
            <div class="card h-100 shadow-sm" style="border-radius:10px;overflow:hidden;border:none">
                <div style="position:relative">
                    <?php if($item->product->image): ?>
                    <img src="<?php echo e(asset('storage/'.$item->product->image)); ?>" class="card-img-top" style="height:180px;object-fit:cover" alt="<?php echo e($item->product->name); ?>">
                    <?php else: ?>
                    <div style="height:180px;background:#f1f5f9;display:flex;align-items:center;justify-content:center">
                        <i class="fas fa-box" style="font-size:36px;color:#cbd5e0"></i>
                    </div>
                    <?php endif; ?>
                    <button onclick="removeWish(<?php echo e($item->product_id); ?>)"
                        style="position:absolute;top:8px;right:8px;background:rgba(255,255,255,.9);border:none;border-radius:50%;width:30px;height:30px;cursor:pointer;color:#e74c3c;font-size:15px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,.1)"
                        title="Remove from wishlist">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="card-body p-2">
                    <div style="font-size:12px;font-weight:600;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="<?php echo e($item->product->name); ?>">
                        <?php echo e($item->product->name); ?>

                    </div>
                    <div style="font-size:13px;font-weight:700;color:#e74c3c;margin-top:4px">
                        $<?php echo e(number_format($item->product->price, 2)); ?>

                    </div>
                    <?php if($item->product->original_price > $item->product->price): ?>
                    <div style="font-size:11px;color:#9ca3af;text-decoration:line-through">
                        $<?php echo e(number_format($item->product->original_price, 2)); ?>

                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0 pb-2 px-2 d-flex gap-1">
                    <a href="<?php echo e(route('product.show', $item->product_id)); ?>" class="btn btn-sm btn-outline-secondary flex-fill" style="font-size:11px">Details</a>
                    <form action="<?php echo e(route('cart.add', $item->product_id)); ?>" method="POST" class="flex-fill">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-primary w-100" style="font-size:11px">
                            <i class="fas fa-cart-plus"></i> Add to cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</div>

<script>
function removeWish(productId) {
    fetch(`/wishlist/toggle/${productId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Content-Type': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            const card = document.getElementById('wish-card-' + productId);
            if (card) {
                card.style.transition = 'opacity .3s';
                card.style.opacity = '0';
                setTimeout(() => card.remove(), 300);
            }
        }
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\wishlist.blade.php ENDPATH**/ ?>