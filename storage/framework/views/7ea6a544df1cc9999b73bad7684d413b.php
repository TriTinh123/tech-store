
<?php $__env->startSection('page_title', 'Checkout'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
<style>
.checkout-section { background:#fff;border-radius:14px;border:1px solid var(--border);padding:28px;margin-bottom:20px; }
.section-label { font-size:15px;font-weight:700;padding-bottom:12px;margin-bottom:20px;border-bottom:2px solid var(--green);display:flex;align-items:center;gap:8px; }
.section-label i { color:var(--green); }
.form-control:focus,.form-select:focus { border-color:var(--green);box-shadow:0 0 0 .2rem rgba(0,184,148,.18); }
.pay-option { display:flex;align-items:center;padding:14px 16px;border:2px solid var(--border);border-radius:10px;cursor:pointer;transition:.2s;margin-bottom:10px; }
.pay-option:hover,.pay-option.active { border-color:var(--green);background:#f0fdf4; }
.pay-option input { margin-right:14px;accent-color:var(--green);width:18px;height:18px; }
.pay-icon { font-size:22px;margin-right:14px;color:var(--green);width:28px;text-align:center; }
.summary-card { background:#fff;border-radius:14px;border:1px solid var(--border);padding:24px;position:sticky;top:90px; }
.summary-item { display:flex;align-items:flex-start;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px; }
.summary-item:last-of-type { border-bottom:none; }
.summary-total { font-size:18px;font-weight:800;color:var(--danger); }
.btn-place-order { display:block;width:100%;padding:15px;background:linear-gradient(135deg,#00b894,#00cec9);color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:700;cursor:pointer;transition:.2s; }
.btn-place-order:hover { opacity:.9; }
.btn-place-order:disabled { background:#cbd5e0;cursor:not-allowed; }
.coupon-box { border:1.5px dashed var(--border);border-radius:10px;padding:14px 16px;background:#fafafa;margin-top:14px; }
</style>

<?php if($errors->any()): ?>
<div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
    <strong><i class="fas fa-exclamation-triangle me-2"></i>Please review:</strong>
    <ul class="mb-0 mt-2 ps-3"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li style="font-size:13px"><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form action="<?php echo e(route('checkout.store')); ?>" method="POST" id="checkoutForm">
    <?php echo csrf_field(); ?>
    <div class="row g-4">

        
        <div class="col-lg-8">

            
            <div class="checkout-section">
                <div class="section-label"><i class="fas fa-user"></i>Customer Information</div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-600" style="font-size:13px">Full name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" value="<?php echo e(old('customer_name')); ?>" class="form-control" required placeholder="John Doe">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-600" style="font-size:13px">Email <span class="text-danger">*</span></label>
                        <input type="email" name="customer_email" value="<?php echo e(old('customer_email')); ?>" class="form-control" required placeholder="email@example.com">
                    </div>
                    <div class="col-sm-12">
                        <label class="form-label fw-600" style="font-size:13px">Phone number <span class="text-danger">*</span></label>
                        <input type="tel" name="customer_phone" value="<?php echo e(old('customer_phone')); ?>" class="form-control" required placeholder="0912 345 678" id="phone">
                    </div>
                </div>
            </div>

            
            <div class="checkout-section">
                <div class="section-label"><i class="fas fa-map-marker-alt"></i>Delivery Address</div>
                <label class="form-label fw-600" style="font-size:13px">Delivery address <span class="text-danger">*</span></label>
                <textarea name="delivery_address" class="form-control" rows="3" required placeholder="Street address, ward, district, city"><?php echo e(old('delivery_address')); ?></textarea>
            </div>

            
            <div class="checkout-section">
                <div class="section-label"><i class="fas fa-wallet"></i>Payment Method</div>
                <label class="pay-option <?php echo e(old('payment_method')=='cod'||!old('payment_method')?'active':''); ?>" onclick="selectPay(this)">
                    <input type="radio" name="payment_method" value="cod" <?php echo e(old('payment_method','cod')=='cod'?'checked':''); ?> required>
                    <i class="fas fa-truck pay-icon"></i>
                    <div><div class="fw-600" style="font-size:14px">Cash on Delivery (COD)</div><div class="text-muted" style="font-size:12px">Pay cash upon delivery</div></div>
                </label>
                <label class="pay-option <?php echo e(old('payment_method')=='bank_transfer'?'active':''); ?>" onclick="selectPay(this)">
                    <input type="radio" name="payment_method" value="bank_transfer" <?php echo e(old('payment_method')=='bank_transfer'?'checked':''); ?> required>
                    <i class="fas fa-university pay-icon"></i>
                    <div><div class="fw-600" style="font-size:14px">Bank Transfer</div><div class="text-muted" style="font-size:12px">Transfer before delivery</div></div>
                </label>
                <label class="pay-option <?php echo e(old('payment_method')=='e_wallet'?'active':''); ?>" onclick="selectPay(this)">
                    <input type="radio" name="payment_method" value="e_wallet" <?php echo e(old('payment_method')=='e_wallet'?'checked':''); ?> required>
                    <i class="fas fa-mobile-alt pay-icon"></i>
                    <div><div class="fw-600" style="font-size:14px">E-Wallet (Momo, ZaloPay, ...)</div><div class="text-muted" style="font-size:12px">Pay via e-wallet app</div></div>
                </label>
            </div>

            
            <div class="checkout-section">
                <div class="section-label"><i class="fas fa-sticky-note"></i>Notes</div>
                <label class="form-label fw-600" style="font-size:13px">Additional notes (optional)</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Morning delivery, leave at door..."><?php echo e(old('notes')); ?></textarea>
            </div>
        </div>

        
        <div class="col-lg-4">
            <div class="summary-card">
                <h6 class="fw-700 mb-3"><i class="fas fa-receipt me-2 text-success"></i>Order Summary</h6>
                <div>
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="summary-item">
                        <span style="flex:1;color:var(--text)"><?php echo e(Str::limit($item['product']->name,35)); ?></span>
                        <span class="text-muted ms-2">x<?php echo e($item['quantity']); ?></span>
                        <span class="ms-3 fw-600" style="color:var(--danger);min-width:90px;text-align:right">$<?php echo e(number_format($item['subtotal'], 2)); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="d-flex justify-content-between mt-3 pt-2" style="border-top:1px solid var(--border);font-size:14px">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-600" id="subtotal-display">$<?php echo e(number_format($total, 2)); ?></span>
                </div>
                <div class="d-flex justify-content-between mt-2" style="font-size:14px">
                    <span class="text-muted">Shipping</span>
                    <span class="fw-700 text-success">FREE</span>
                </div>

                
                <div class="coupon-box mt-3">
                    <div class="fw-600 mb-2" style="font-size:12px;color:var(--text-m)"><i class="fas fa-tag me-1 text-warning"></i>Coupon code</div>
                    <div class="d-flex gap-2">
                        <input type="text" id="coupon-input" placeholder="Enter coupon code" class="form-control form-control-sm" style="text-transform:uppercase">
                        <button type="button" id="coupon-btn" onclick="applyCoupon()" class="btn btn-sm btn-success px-3 rounded-pill fw-600 text-nowrap">Apply</button>
                    </div>
                    <div id="coupon-msg" style="font-size:12px;margin-top:6px;display:none"></div>
                </div>
                <input type="hidden" name="coupon_code" id="coupon-code-hidden" value="">
                <input type="hidden" name="discount_amount" id="discount-amount-hidden" value="0">
                <div class="d-flex justify-content-between mt-2" id="discount-row" style="display:none!important;font-size:13px">
                    <span class="text-muted">Discount (<span id="discount-code-label"></span>)</span>
                    <span class="fw-700 text-danger" id="discount-display">-$0.00</span>
                </div>

                <div class="d-flex justify-content-between mt-3 pt-3 summary-total" style="border-top:2px solid var(--border)">
                    <span>Total</span>
                    <span id="total-display">$<?php echo e(number_format($total, 2)); ?></span>
                </div>

                <button type="submit" class="btn-place-order mt-4">
                    <i class="fas fa-check-circle me-2"></i>Order Confirmation
                </button>
                <a href="<?php echo e(route('cart.index')); ?>" class="btn btn-outline-secondary w-100 rounded-pill mt-2 fw-600">
                    <i class="fas fa-arrow-left me-2"></i>Back to cart
                </a>

                <div class="mt-3 p-3 rounded-3" style="background:#f4f7fa;font-size:12px">
                    <div class="d-flex gap-2 mb-1"><i class="fas fa-shield-alt text-success"></i><span>Secure & safe checkout</span></div>
                    <div class="d-flex gap-2 mb-1"><i class="fas fa-envelope text-success"></i><span>A confirmation email will be sent to you</span></div>
                    <div class="d-flex gap-2"><i class="fas fa-headset text-success"></i><span>24/7 Support: 1900-1234</span></div>
                </div>
            </div>
        </div>
    </div>
</form>
</div>

<script>
function selectPay(el) {
    document.querySelectorAll('.pay-option').forEach(e=>e.classList.remove('active'));
    el.classList.add('active');
    el.querySelector('input').checked=true;
}
document.getElementById('checkoutForm').addEventListener('submit',function(e){
    const ph=document.getElementById('phone').value.replace(/\s/g,'');
    if(!/^[0-9]{10,11}$/.test(ph)){e.preventDefault();alert('Invalid phone number (10-11 digits).');return false;}
});
const baseTotal = <?php echo e($total); ?>;
let appliedDiscount=0;
function fmtVnd(n){return '$' + Number(n).toFixed(2);}
function applyCoupon(){
    const code=document.getElementById('coupon-input').value.trim();
    if(!code){showCouponMsg('Please enter a coupon code.',false);return;}
    fetch('<?php echo e(route("coupon.apply")); ?>',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'},body:JSON.stringify({code,total:baseTotal})})
    .then(r=>r.json()).then(data=>{
        if(data.ok){
            appliedDiscount=data.discount;
            document.getElementById('coupon-code-hidden').value=code.toUpperCase();
            document.getElementById('discount-amount-hidden').value=data.discount;
            document.getElementById('discount-code-label').textContent=code.toUpperCase();
            document.getElementById('discount-display').textContent='-'+fmtVnd(data.discount);
            document.getElementById('total-display').textContent=fmtVnd(data.final);
            document.getElementById('discount-row').style.removeProperty('display');
            document.getElementById('coupon-btn').textContent='Applied ✓';
            document.getElementById('coupon-btn').style.background='#27ae60';
            document.getElementById('coupon-input').disabled=true;
            showCouponMsg(data.message,true);
        }else{showCouponMsg(data.message,false);}
    }).catch(()=>showCouponMsg('Connection error. Please try again.',false));
}
function showCouponMsg(txt,ok){const el=document.getElementById('coupon-msg');el.style.display='block';el.style.color=ok?'#16a34a':'#dc2626';el.textContent=txt;}
document.getElementById('coupon-input').addEventListener('keydown',function(e){if(e.key==='Enter'){e.preventDefault();applyCoupon();}});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/checkout.blade.php ENDPATH**/ ?>