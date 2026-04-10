
<?php $__env->startSection('page_title', 'Order placed'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-5">
<style>
.success-hero { text-align:center;padding:48px 20px 36px; }
.success-icon { width:90px;height:90px;background:linear-gradient(135deg,#00b894,#00cec9);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:40px;color:#fff;box-shadow:0 10px 30px rgba(0,184,148,.35); }
.success-badge { display:inline-flex;align-items:center;gap:6px;background:#d1fae5;color:#065f46;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:600;margin-bottom:12px; }
.os-card { background:#fff;border-radius:16px;border:1px solid var(--border);overflow:hidden;margin-bottom:20px; }
.os-card-head { background:#f8fafc;padding:14px 20px;border-bottom:1px solid var(--border);font-weight:700;font-size:14px;display:flex;align-items:center;gap:8px; }
.os-card-head i { color:var(--green); }
.info-row { display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);font-size:14px; }
.info-row:last-of-type{border-bottom:none;}
.info-label{color:var(--text-m);min-width:140px;}
.info-val{font-weight:600;text-align:right;}
.order-item { display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid var(--border); }
.order-item:last-of-type{border-bottom:none;}
.order-item img{width:60px;height:60px;object-fit:contain;background:#f4f7fa;border-radius:10px;padding:4px;}
.step-item{display:flex;gap:14px;align-items:flex-start;padding:10px 0;border-bottom:1px solid var(--border);}
.step-item:last-of-type{border-bottom:none;}
.step-num{width:28px;height:28px;background:var(--green);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;margin-top:2px;}
.btn-primary-ts{display:block;width:100%;padding:14px;background:linear-gradient(135deg,#00b894,#00cec9);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;text-align:center;text-decoration:none;cursor:pointer;transition:.2s;margin-bottom:10px;}
.btn-primary-ts:hover{opacity:.9;color:#fff;}
.btn-outline-ts{display:block;width:100%;padding:11px;background:#fff;color:var(--green);border:2px solid var(--green);border-radius:10px;font-size:14px;font-weight:600;text-align:center;text-decoration:none;transition:.2s;margin-bottom:10px;}
.btn-outline-ts:hover{background:var(--green);color:#fff;}
@keyframes popIn{0%{transform:scale(.4);opacity:0}70%{transform:scale(1.12)}100%{transform:scale(1);opacity:1}}
.success-icon{animation:popIn .5s ease-out both;}
/* ── Shopee tracker ── */
.os-tracker{background:#fff;border-radius:16px;border:1px solid var(--border);padding:22px 24px 14px;margin-bottom:20px;overflow:hidden;}
.os-tracker-title{font-size:13px;color:var(--text-m);font-weight:600;margin-bottom:18px;display:flex;align-items:center;gap:6px;}
.os-tracker-title i{color:var(--green);}
/* Horizontal bar */
.os-hbar{display:flex;align-items:flex-start;justify-content:space-between;position:relative;margin-bottom:6px;}
.os-hbar::before{content:'';position:absolute;top:19px;left:calc(50%/4 + 10px);right:calc(50%/4 + 10px);height:4px;background:var(--border);border-radius:2px;z-index:0;}
.os-hbar-fill{position:absolute;top:19px;left:calc(50%/4 + 10px);height:4px;border-radius:2px;background:linear-gradient(90deg,#00b894,#0984e3);z-index:1;transition:width .8s cubic-bezier(.4,0,.2,1);}
.os-hbar-step{display:flex;flex-direction:column;align-items:center;gap:6px;z-index:2;flex:1;}
.os-hbar-dot{width:38px;height:38px;border-radius:50%;border:3px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;color:#b2bec3;transition:all .4s;}
.os-hbar-step.done .os-hbar-dot{background:var(--green);border-color:var(--green);color:#fff;}
.os-hbar-step.cur .os-hbar-dot{background:var(--blue);border-color:var(--blue);color:#fff;box-shadow:0 0 0 6px rgba(9,132,227,.15);transform:scale(1.1);}
.os-hbar-lbl{font-size:.72rem;color:var(--text-m);white-space:nowrap;font-weight:500;}
.os-hbar-step.done .os-hbar-lbl{color:var(--green);font-weight:700;}
.os-hbar-step.cur .os-hbar-lbl{color:var(--blue);font-weight:700;}
/* Vertical timeline */
.os-vtl{display:flex;flex-direction:column;}
.os-vt-step{display:flex;gap:0;}
.os-vt-line{display:flex;flex-direction:column;align-items:center;width:46px;flex-shrink:0;}
.os-vt-dot{width:38px;height:38px;border-radius:50%;border:3px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;font-size:.95rem;color:#b2bec3;transition:all .3s;flex-shrink:0;}
.os-vt-step.done .os-vt-dot{color:#fff;}
.os-vt-step.cur .os-vt-dot{box-shadow:0 0 0 6px rgba(9,132,227,.12);transform:scale(1.05);}
.os-vt-conn{flex:1;width:3px;background:var(--border);min-height:30px;transition:background .4s;}
.os-vt-conn.filled{background:var(--green);}
.os-vt-body{padding:2px 0 26px 16px;flex:1;}
.os-vt-step:last-child .os-vt-body{padding-bottom:4px;}
.os-vt-label{font-size:.95rem;font-weight:600;color:#b2bec3;display:flex;align-items:center;gap:8px;margin-bottom:3px;}
.os-vt-step.done .os-vt-label{color:var(--text);}
.os-cur-tag{font-size:.68rem;padding:2px 8px;border-radius:12px;color:#fff;font-weight:700;}
.os-vt-sub{font-size:.83rem;color:var(--text-m);margin-bottom:3px;line-height:1.4;}
.os-vt-time{font-size:.77rem;color:#adb5bd;display:flex;align-items:center;gap:4px;}
.os-vt-step.done .os-vt-time{color:var(--text-m);}
.os-eta-badge{display:inline-flex;align-items:center;gap:6px;background:#f0f9ff;border:1px solid #bae6fd;color:#0369a1;padding:7px 14px;border-radius:10px;font-size:13px;font-weight:600;margin-top:10px;}
</style>


<div class="success-hero">
    <div class="success-icon"><i class="fas fa-check"></i></div>
    <span class="success-badge"><i class="fas fa-circle-check"></i>Payment successful</span>
    <h2 class="fw-800 mb-1" style="color:var(--text)">Order Placed Successfully! 🎉</h2>
    <p class="text-muted" style="font-size:15px">Thank you for trusting <strong>TechStore</strong>.<br>Your order is being processed.</p>
    <span class="os-eta-badge"><i class="fas fa-shipping-fast"></i>Estimated delivery: <strong><?php echo e($order->created_at->addDays(3)->format('d/m/Y')); ?> – <?php echo e($order->created_at->addDays(5)->format('d/m/Y')); ?></strong></span>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-7">

        
        <?php
            $statusIdx = ['pending'=>0,'confirmed'=>1,'shipped'=>2,'delivered'=>3,'cancelled'=>-1];
            $sIdx = $statusIdx[$order->status] ?? 0;
            $t = $order->created_at;
            $hbarPct = [0=>8, 1=>35, 2=>68, 3=>100];
            $hbarW   = $hbarPct[$sIdx] ?? 8;
            $vtSteps = [
                ['label'=>'Order placed',
                 'sub'  =>'Order <strong>#'.$order->order_number.'</strong> has been received',
                 'time' =>$t->format('H:i, d/m/Y'),
                 'done' =>true, 'cur'=>$sIdx===0,
                 'icon' =>'fa-shopping-bag','color'=>'#00b894'],
                ['label'=>'Order confirmed',
                 'sub'  =>'Seller confirmed and started preparing the order',
                 'time' =>$sIdx>=1 ? $t->copy()->addHours(1)->format('H:i, d/m/Y') : 'Expected within 1–2 hours',
                 'done' =>$sIdx>=1,'cur'=>$sIdx===1,
                 'icon' =>'fa-clipboard-check','color'=>'#0984e3'],
                ['label'=>'Packed & handed to courier',
                 'sub'  =>$order->tracking_number ? 'Tracking number: <strong>'.$order->tracking_number.'</strong>' : 'Order is being carefully packed',
                 'time' =>$sIdx>=2 ? $t->copy()->addDays(1)->format('H:i, d/m/Y') : 'Expected within 4–8 hours',
                 'done' =>$sIdx>=2,'cur'=>$sIdx===2,
                 'icon' =>'fa-box','color'=>'#6c5ce7'],
                ['label'=>'On the way to you',
                 'sub'  =>'Courier is on the way to your address',
                 'time' =>$sIdx>=2 ? $t->copy()->addDays(2)->format('H:i, d/m/Y') : 'Est. ' . $t->copy()->addDays(2)->format('d/m'),
                 'done' =>$sIdx>=2,'cur'=>false,
                 'icon' =>'fa-truck','color'=>'#e17055'],
                ['label'=>'Delivered successfully',
                 'sub'  =>'You have received your order. Thank you for shopping!',
                 'time' =>$sIdx>=3 ? $order->updated_at->format('H:i, d/m/Y') : 'Est. ' . $t->copy()->addDays(3)->format('d/m/Y') . ' – ' . $t->copy()->addDays(5)->format('d/m/Y'),
                 'done' =>$sIdx>=3,'cur'=>$sIdx===3,
                 'icon' =>'fa-home','color'=>'#00b894'],
            ];
        ?>
        <div class="os-tracker">
          <div class="os-tracker-title"><i class="fas fa-map-marker-alt"></i>Track Order</div>
          
          <div class="os-hbar">
            <div class="os-hbar-fill" style="width:<?php echo e($hbarW); ?>%"></div>
            <?php $__currentLoopData = ['Place Order','Confirm','Shipping','Received']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hi => $hl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="os-hbar-step <?php echo e($sIdx > $hi ? 'done' : ($sIdx === $hi ? 'cur' : '')); ?>">
                <div class="os-hbar-dot"><?php echo e($sIdx > $hi ? '✓' : ($hi+1)); ?></div>
                <div class="os-hbar-lbl"><?php echo e($hl); ?></div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          
          <div class="os-vtl" style="margin-top:20px">
            <?php $__currentLoopData = $vtSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vi => $vs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="os-vt-step <?php echo e($vs['done'] ? 'done' : ''); ?> <?php echo e($vs['cur'] ? 'cur' : ''); ?>">
              <div class="os-vt-line">
                <div class="os-vt-dot" style="<?php echo e($vs['done'] ? 'background:'.$vs['color'].';border-color:'.$vs['color'] : ''); ?>">
                  <?php if($vs['done']): ?>
                    <i class="fas <?php echo e($vs['cur'] ? $vs['icon'] : 'fa-check'); ?>"></i>
                  <?php else: ?>
                    <i class="fas <?php echo e($vs['icon']); ?>"></i>
                  <?php endif; ?>
                </div>
                <?php if($vi < count($vtSteps)-1): ?>
                <div class="os-vt-conn <?php echo e($vs['done'] && $vtSteps[$vi+1]['done'] ? 'filled' : ''); ?>"></div>
                <?php endif; ?>
              </div>
              <div class="os-vt-body">
                <div class="os-vt-label" style="<?php echo e($vs['done'] ? 'color:var(--text)' : ''); ?><?php echo e($vs['cur'] ? ';color:'.$vs['color'] : ''); ?>">
                  <?php echo e($vs['label']); ?>

                  <?php if($vs['cur']): ?><span class="os-cur-tag" style="background:<?php echo e($vs['color']); ?>">Current</span><?php endif; ?>
                </div>
                <div class="os-vt-sub"><?php echo $vs['sub']; ?></div>
                <div class="os-vt-time"><i class="fas fa-clock"></i> <?php echo e($vs['time']); ?></div>
              </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        </div>

        
        <div class="os-card">
            <div class="os-card-head"><i class="fas fa-file-invoice"></i>Order Info</div>
            <div class="px-4 py-2">
                <div class="info-row">
                    <span class="info-label">Order ID</span>
                    <span class="info-val" style="color:var(--blue);font-size:16px">#<?php echo e($order->order_number); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Date</span>
                    <span class="info-val"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment</span>
                    <span class="info-val"><?php echo e(ucfirst(str_replace('_',' ',$order->payment_method))); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Status</span>
                    <span class="info-val">
                        <?php if($order->payment_status === 'completed' || $order->payment_status === 'paid'): ?>
                            <span style="background:#d1fae5;color:#065f46;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700"><i class="fas fa-check me-1"></i>Paid</span>
                        <?php else: ?>
                            <span style="background:#fef9c3;color:#92400e;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700"><i class="fas fa-clock me-1"></i>Awaiting payment</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order status</span>
                    <span class="info-val">
                        <span style="background:#dbeafe;color:#1e40af;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700"><?php echo e(ucfirst($order->status)); ?></span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total</span>
                    <span class="info-val" style="color:var(--danger);font-size:18px;font-weight:800">$<?php echo e(number_format($order->total_amount, 2)); ?></span>
                </div>
            </div>
        </div>

        
        <div class="os-card">
            <div class="os-card-head"><i class="fas fa-box-open"></i>Ordered Items <span class="badge rounded-pill text-bg-light ms-1"><?php echo e($order->items->count()); ?></span></div>
            <div class="px-4 py-2">
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="order-item">
                    <img src="<?php echo e($item->product->image ?? asset('images/no-image.svg')); ?>" alt="<?php echo e($item->product_name); ?>">
                    <div style="flex:1">
                        <div class="fw-600" style="font-size:14px"><?php echo e($item->product_name); ?></div>
                        <div class="text-muted" style="font-size:12px">Quantity: <?php echo e($item->quantity); ?></div>
                    </div>
                    <div class="fw-700" style="color:var(--danger);font-size:14px">$<?php echo e(number_format($item->subtotal, 2)); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="d-flex justify-content-between py-3 mt-1" style="border-top:2px solid var(--border);font-size:16px;font-weight:800;color:var(--danger)">
                    <span>Total</span>
                    <span>$<?php echo e(number_format($order->total_amount, 2)); ?></span>
                </div>
            </div>
        </div>

        
        <div class="os-card">
            <div class="os-card-head"><i class="fas fa-map-marker-alt"></i>Delivery Address</div>
            <div class="px-4 py-3">
                <div class="fw-700 mb-1"><?php echo e($order->customer_name); ?></div>
                <div class="text-muted" style="font-size:14px"><?php echo e($order->delivery_address); ?></div>
                <div class="mt-1" style="font-size:14px"><i class="fas fa-phone me-2 text-success"></i><?php echo e($order->customer_phone); ?></div>
                <div style="font-size:14px"><i class="fas fa-envelope me-2 text-success"></i><?php echo e($order->customer_email); ?></div>
            </div>
        </div>

        
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
          <div style="width:36px;height:36px;background:#00b894;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0">
            <i class="fas fa-envelope"></i>
          </div>
          <div>
            <div style="font-size:13px;font-weight:700;color:#065f46">Confirmation email sent</div>
            <div style="font-size:12px;color:#047857">Check your inbox <strong><?php echo e($order->customer_email); ?></strong> for order details</div>
          </div>
        </div>

        
        <a href="<?php echo e(route('orders.show', $order)); ?>" class="btn-primary-ts">
            <i class="fas fa-box me-2"></i>View order details
        </a>
        <a href="<?php echo e(route('orders.index')); ?>" class="btn-outline-ts">
            <i class="fas fa-list me-2"></i>All my orders
        </a>
        <a href="<?php echo e(route('home')); ?>" class="btn-outline-ts" style="border-color:var(--border);color:var(--text-m)">
            <i class="fas fa-home me-2"></i>Continue shopping
        </a>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\order-success.blade.php ENDPATH**/ ?>