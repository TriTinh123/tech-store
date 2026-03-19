
<?php $__env->startSection('page_title', 'Cart'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
<style>
.cart-table img { width:70px;height:70px;object-fit:contain;border-radius:8px;background:#f4f7fa; }
.cart-qty-btn { width:32px;height:32px;border:1px solid var(--border);background:#fff;border-radius:6px;cursor:pointer;font-size:16px;line-height:1; }
.cart-qty-input { width:46px;text-align:center;border:1px solid var(--border);border-radius:6px;padding:4px 0;font-size:14px; }
.summary-card { background:#fff;border-radius:14px;border:1px solid var(--border);padding:24px;position:sticky;top:90px; }
.summary-row { display:flex;justify-content:space-between;align-items:center;padding:8px 0;font-size:14px;border-bottom:1px solid var(--border); }
.summary-row:last-of-type { border-bottom:none; }
.summary-total { font-size:18px;font-weight:700;color:var(--danger); }
.btn-checkout { display:block;width:100%;padding:14px;background:linear-gradient(135deg,#00b894,#00cec9);color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:700;text-align:center;cursor:pointer;text-decoration:none;transition:.2s; }
.btn-checkout:hover { opacity:.9;color:#fff; }
.btn-clear { display:block;width:100%;padding:10px;background:#fff;color:#e84040;border:1.5px solid #e84040;border-radius:10px;font-size:14px;font-weight:600;text-align:center;cursor:pointer;text-decoration:none;margin-top:10px;transition:.2s; }
.btn-clear:hover { background:#e84040;color:#fff; }
.empty-cart-box { background:#fff;border-radius:16px;border:1px solid var(--border);padding:80px 30px;text-align:center; }
.cart-remove { background:none;border:none;color:#cbd5e0;cursor:pointer;font-size:18px;transition:color .2s; }
.cart-remove:hover { color:#e84040; }
.item-subtotal { font-weight:700;color:var(--danger);min-width:110px;text-align:right; }
</style>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
    <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(count($items) > 0): ?>
<div class="row g-4">
    
    <div class="col-lg-8">
        <div class="bg-white rounded-4 border" style="border-color:var(--border)!important">
            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                <h5 class="mb-0 fw-700"><i class="fas fa-shopping-cart me-2 text-success"></i>Your Cart</h5>
                <span class="badge rounded-pill" style="background:var(--green)"><?php echo e(count($items)); ?> products</span>
            </div>

            <div class="table-responsive">
                <table class="table cart-table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width:80px">Image</th>
                            <th>Products</th>
                            <th class="text-center" style="width:120px">Unit Price</th>
                            <th class="text-center" style="width:130px">Quantity</th>
                            <th class="text-end" style="width:120px">Subtotal</th>
                            <th class="text-center" style="width:60px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-4">
                                <img src="<?php echo e($item['product']->image ?? asset('images/no-image.svg')); ?>" alt="<?php echo e($item['product']->name); ?>">
                            </td>
                            <td>
                                <a href="<?php echo e(route('product.show', $item['product']->id)); ?>" class="fw-600 text-decoration-none" style="color:var(--text)"><?php echo e($item['product']->name); ?></a>
                                <div class="text-muted" style="font-size:12px">SKU: #<?php echo e($item['product']->id); ?></div>
                            </td>
                            <td class="text-center" style="color:var(--danger);font-weight:600">
                                $<?php echo e(number_format($item['product']->price, 2)); ?>

                            </td>
                            <td class="text-center">
                                <form action="<?php echo e(route('cart.update', $item['product']->id)); ?>" method="POST" class="d-flex align-items-center justify-content-center gap-1">
                                    <?php echo csrf_field(); ?>
                                    <button type="button" class="cart-qty-btn" onclick="stepQty(this,-1)">−</button>
                                    <input type="number" name="quantity" value="<?php echo e($item['quantity']); ?>" min="1" class="cart-qty-input" onchange="this.form.submit()">
                                    <button type="button" class="cart-qty-btn" onclick="stepQty(this,1)">+</button>
                                </form>
                            </td>
                            <td>
                                <div class="item-subtotal">$<?php echo e(number_format($item['product']->price * $item['quantity'], 2)); ?></div>
                            </td>
                            <td class="text-center">
                                <form action="<?php echo e(route('cart.remove', $item['product']->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="cart-remove" title="Remove item">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 d-flex gap-3 flex-wrap">
                <a href="<?php echo e(url('/')); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i>Continue shopping
                </a>
            </div>
        </div>
    </div>

    
    <div class="col-lg-4">
        <div class="summary-card">
            <h6 class="fw-700 mb-3"><i class="fas fa-receipt me-2 text-success"></i>Order Summary</h6>

            <div class="summary-row">
                <span class="text-muted">Subtotal</span>
                <span class="fw-600">$<?php echo e(number_format($total, 2)); ?></span>
            </div>
            <div class="summary-row">
                <span class="text-muted">Shipping</span>
                <span class="fw-600 text-success">Free</span>
            </div>
            <div class="summary-row summary-total mt-2 pt-2" style="border-top:2px solid var(--border)">
                <span>Total</span>
                <span>$<?php echo e(number_format($total, 2)); ?></span>
            </div>

            <a href="<?php echo e(route('checkout.show')); ?>" class="btn-checkout mt-4">
                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
            </a>

            <form action="<?php echo e(route('cart.clear')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-clear" onclick="return confirm('Clear entire cart?')">
                    <i class="fas fa-trash me-2"></i>Clear Cart
                </button>
            </form>

            <div class="mt-4 p-3 rounded-3" style="background:#f4f7fa;font-size:13px">
                <div class="d-flex gap-2 mb-1"><i class="fas fa-shield-alt text-success mt-1"></i><span>Secure & safe checkout</span></div>
                <div class="d-flex gap-2 mb-1"><i class="fas fa-truck text-success mt-1"></i><span>Free shipping nationwide</span></div>
                <div class="d-flex gap-2"><i class="fas fa-sync-alt text-success mt-1"></i><span>Returns within 30 days</span></div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<div class="empty-cart-box">
    <div style="font-size:72px;color:#e2e8f0;line-height:1"><i class="fas fa-shopping-cart"></i></div>
    <h4 class="mt-3 fw-700">Your cart is empty</h4>
    <p class="text-muted mb-4">Add products to continue shopping!</p>
    <a href="<?php echo e(url('/')); ?>" class="btn btn-success rounded-pill px-5 py-2 fw-600">
        <i class="fas fa-arrow-left me-2"></i>Continue shopping
    </a>
</div>
<?php endif; ?>
</div>

<script>
function stepQty(btn, dir) {
    const form  = btn.closest('form');
    const input = form.querySelector('input[name=quantity]');
    const newV  = Math.max(1, parseInt(input.value) + dir);
    input.value = newV;
    form.submit();
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/cart.blade.php ENDPATH**/ ?>