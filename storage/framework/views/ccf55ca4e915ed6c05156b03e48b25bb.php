
<?php $__env->startSection('title', 'Order Details #' . ($order->order_number ?? $order->id)); ?>

<?php $__env->startSection('body-content'); ?>
<?php
    $statusCfg = [
        'pending'   => ['label'=>'Pending confirmation',    'color'=>'#f39c12','bg'=>'#fff9ec','icon'=>'fa-clock',        'pct'=>8],
        'confirmed' => ['label'=>'Confirmed',     'color'=>'#0984e3','bg'=>'#e8f4fd','icon'=>'fa-check',        'pct'=>35],
        'shipped'   => ['label'=>'Shipping',  'color'=>'#6c5ce7','bg'=>'#f0eeff','icon'=>'fa-truck',        'pct'=>68],
        'delivered' => ['label'=>'Delivered','color'=>'#00b894','bg'=>'#e6faf5','icon'=>'fa-check-circle','pct'=>100],
        'cancelled' => ['label'=>'Cancelled',      'color'=>'#e84040','bg'=>'#ffeaea','icon'=>'fa-times-circle', 'pct'=>0],
    ];
    $st  = $order->status ?? 'pending';
    $sc  = $statusCfg[$st] ?? $statusCfg['pending'];
    $idx = ['pending'=>0,'confirmed'=>1,'shipped'=>2,'delivered'=>3,'cancelled'=>-1][$st] ?? 0;
    // Ensure Carbon instances regardless of model cast
    $t   = $order->created_at instanceof \Carbon\Carbon ? $order->created_at : \Carbon\Carbon::parse($order->created_at);
    $upd = $order->updated_at instanceof \Carbon\Carbon ? $order->updated_at : \Carbon\Carbon::parse($order->updated_at);
    $stepsTimeline = [
        ['label'=>'Order placed',              'sub' =>'Order #'.($order->order_number??$order->id).' received','time'=>$t->format('H:i, d/m/Y'),'done'=>true, 'cur'=>$idx===0,'icon'=>'fa-shopping-bag','color'=>'#00b894'],
        ['label'=>'Order Confirmation',                'sub' =>'Seller confirmed & preparing','time'=>$idx>=1?$t->copy()->addHours(1)->format('H:i, d/m/Y'):'Pending confirmation','done'=>$idx>=1,'cur'=>$idx===1,'icon'=>'fa-clipboard-check','color'=>'#0984e3'],
        ['label'=>'Pack & ship',     'sub' =>$order->tracking_number?'Tracking number: '.$order->tracking_number.($order->shipping_provider?' — '.$order->shipping_provider:''):'Packing order','time'=>$idx>=2?$t->copy()->addDays(1)->format('H:i, d/m/Y'):'Pending','done'=>$idx>=2,'cur'=>$idx===2,'icon'=>'fa-box','color'=>'#6c5ce7'],
        ['label'=>'Delivered successfully',             'sub' =>'Customer received the order','time'=>$idx>=3?$upd->format('H:i, d/m/Y'):'Est. '.$t->copy()->addDays(3)->format('d/m/Y').' – '.$t->copy()->addDays(5)->format('d/m/Y'),'done'=>$idx>=3,'cur'=>$idx===3,'icon'=>'fa-home','color'=>'#00b894'],
    ];
    $payLabels = ['cod'=>'COD','bank_transfer'=>'Bank transfer','momo'=>'MoMo','zalopay'=>'ZaloPay'];
?>

<style>
.aod-wrap{display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;}
.aod-card{background:#fff;border-radius:12px;border:1px solid #e8edf2;margin-bottom:16px;overflow:hidden;}
.aod-card-head{padding:14px 20px;border-bottom:1px solid #e8edf2;font-weight:700;font-size:13.5px;color:#1a1f2e;display:flex;align-items:center;gap:8px;}
.aod-card-head i{color:#0984e3;font-size:13px;}
.aod-card-body{padding:18px 20px;}
/* Timeline */
.aod-tl{display:flex;flex-direction:column;gap:0;}
.aod-tl-step{display:flex;gap:0;}
.aod-tl-line{display:flex;flex-direction:column;align-items:center;width:44px;flex-shrink:0;}
.aod-tl-dot{width:36px;height:36px;border-radius:50%;border:3px solid #e8edf2;background:#fff;display:flex;align-items:center;justify-content:center;font-size:.85rem;color:#b2bec3;flex-shrink:0;transition:.3s;}
.aod-tl-step.done .aod-tl-dot{color:#fff;}
.aod-tl-conn{flex:1;width:3px;background:#e8edf2;min-height:28px;}
.aod-tl-conn.fill{background:#00b894;}
.aod-tl-body{padding:2px 0 24px 14px;flex:1;}
.aod-tl-step:last-child .aod-tl-body{padding-bottom:4px;}
.aod-tl-label{font-size:13px;font-weight:600;color:#b2bec3;display:flex;align-items:center;gap:6px;margin-bottom:2px;}
.aod-tl-step.done .aod-tl-label{color:#1a1f2e;}
.aod-cur-tag{font-size:10px;padding:2px 7px;border-radius:10px;color:#fff;font-weight:700;}
.aod-tl-sub{font-size:12px;color:#94a3b8;margin-bottom:2px;}
.aod-tl-time{font-size:11px;color:#b2bec3;display:flex;align-items:center;gap:3px;}
/* Info rows */
.aod-info-row{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f4f7fa;font-size:13px;}
.aod-info-row:last-child{border-bottom:none;}
.aod-info-lbl{color:#64748b;}
.aod-info-val{font-weight:600;color:#1a1f2e;text-align:right;}
/* Items */
.aod-item{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f4f7fa;}
.aod-item:last-child{border-bottom:none;}
.aod-item-img{width:52px;height:52px;object-fit:cover;border-radius:8px;flex-shrink:0;background:#f4f7fa;}
/* Buttons */
.aod-btn-step{display:block;width:100%;padding:11px;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;margin-bottom:8px;display:flex;align-items:center;justify-content:center;gap:6px;}
.aod-btn-confirm{background:#0984e3;color:#fff;}
.aod-btn-confirm:hover{background:#0773c5;}
.aod-btn-ship{background:#6c5ce7;color:#fff;}
.aod-btn-ship:hover{background:#5a4bd1;}
.aod-btn-deliver{background:#00b894;color:#fff;}
.aod-btn-deliver:hover{background:#00a381;}
.aod-btn-cancel{background:#fff;color:#e84040;border:1.5px solid #e84040;margin-bottom:0;}
.aod-btn-cancel:hover{background:#ffeaea;}
@media(max-width:900px){.aod-wrap{grid-template-columns:1fr;}}
</style>


<div class="pg-hdr" style="margin-bottom:20px">
  <div>
    <h2 style="display:flex;align-items:center;gap:10px">
      Order <span style="color:#0984e3">#<?php echo e($order->order_number ?? $order->id); ?></span>
      <span style="font-size:13px;padding:4px 12px;border-radius:20px;font-weight:700;background:<?php echo e($sc['bg']); ?>;color:<?php echo e($sc['color']); ?>">
        <i class="fas <?php echo e($sc['icon']); ?>" style="font-size:11px"></i> <?php echo e($sc['label']); ?>

      </span>
    </h2>
    <p><?php echo e($t->format('H:i, d/m/Y')); ?> • <?php echo e($order->customer_name); ?> • <?php echo e($order->customer_email); ?></p>
  </div>
  <a href="<?php echo e(route('admin.orders')); ?>" class="btn-icon btn-icon-secondary" style="padding:8px 16px;border-radius:8px;display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600">
    <i class="fas fa-arrow-left"></i> Back to list
  </a>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:10px;font-size:13px">
  <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="aod-wrap">
  
  <div>
    
    <div class="aod-card">
      <div class="aod-card-head"><i class="fas fa-map-marker-alt"></i> Order Progress
        <?php if($order->tracking_number): ?>
        <span style="margin-left:auto;background:#f0f4ff;color:#0984e3;font-size:11px;padding:3px 10px;border-radius:12px;font-weight:600">
          <i class="fas fa-barcode"></i> <?php echo e($order->tracking_number); ?>

        </span>
        <?php endif; ?>
      </div>
      <div class="aod-card-body">
        <div class="aod-tl">
          <?php $__currentLoopData = $stepsTimeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="aod-tl-step <?php echo e($step['done'] ? 'done' : ''); ?>">
            <div class="aod-tl-line">
              <div class="aod-tl-dot" style="<?php echo e($step['done'] ? 'background:'.$step['color'].';border-color:'.$step['color'] : ''); ?>">
                <?php if($step['done']): ?>
                  <i class="fas <?php echo e($step['cur'] ? $step['icon'] : 'fa-check'); ?>" style="font-size:.75rem"></i>
                <?php else: ?>
                  <i class="fas <?php echo e($step['icon']); ?>" style="font-size:.75rem"></i>
                <?php endif; ?>
              </div>
              <?php if($i < count($stepsTimeline)-1): ?>
              <div class="aod-tl-conn <?php echo e($step['done'] && $stepsTimeline[$i+1]['done'] ? 'fill' : ''); ?>"></div>
              <?php endif; ?>
            </div>
            <div class="aod-tl-body">
              <div class="aod-tl-label" style="<?php echo e($step['cur'] ? 'color:'.$step['color'] : ''); ?>">
                <?php echo e($step['label']); ?>

                <?php if($step['cur']): ?><span class="aod-cur-tag" style="background:<?php echo e($step['color']); ?>">Current</span><?php endif; ?>
              </div>
              <div class="aod-tl-sub"><?php echo e($step['sub']); ?></div>
              <div class="aod-tl-time"><i class="fas fa-clock"></i> <?php echo e($step['time']); ?></div>
            </div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>

    
    <div class="aod-card">
      <div class="aod-card-head"><i class="fas fa-box-open"></i> Products (<?php echo e($order->items->count()); ?>)</div>
      <div class="aod-card-body">
        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="aod-item">
          <?php if(isset($item->product->image)): ?>
          <img src="<?php echo e(asset('storage/'.$item->product->image)); ?>" class="aod-item-img"
               onerror="this.src='<?php echo e(asset('images/no-image.svg')); ?>'" alt="">
          <?php else: ?>
          <div class="aod-item-img" style="display:flex;align-items:center;justify-content:center;color:#b2bec3"><i class="fas fa-image"></i></div>
          <?php endif; ?>
          <div style="flex:1">
            <div style="font-size:13px;font-weight:600;color:#1a1f2e"><?php echo e($item->product_name ?? ($item->product->name ?? 'Products')); ?></div>
            <div style="font-size:12px;color:#94a3b8">Unit Price: $<?php echo e(number_format($item->price, 2)); ?> × <?php echo e($item->quantity); ?></div>
          </div>
          <div style="font-size:13px;font-weight:700;color:#e84040;white-space:nowrap">$<?php echo e(number_format($item->subtotal ?? $item->price*$item->quantity, 2)); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div style="border-top:2px solid #f4f7fa;padding-top:14px;margin-top:8px">
          <?php if(($order->discount_amount??0)>0): ?>
          <div class="aod-info-row"><span class="aod-info-lbl">Discount (<?php echo e($order->coupon_code); ?>)</span><span style="color:#00b894;font-weight:700">-$<?php echo e(number_format($order->discount_amount, 2)); ?></span></div>
          <?php endif; ?>
          <div class="aod-info-row"><span class="aod-info-lbl">Shipping</span><span style="color:#00b894;font-weight:700">Free</span></div>
          <div class="aod-info-row" style="font-size:15px;padding-top:12px;border-bottom:none"><span style="font-weight:800;color:#1a1f2e">Total</span><span style="font-weight:800;color:#e84040;font-size:18px">$<?php echo e(number_format($order->total_amount, 2)); ?></span></div>
        </div>
      </div>
    </div>

    
    <div class="aod-card">
      <div class="aod-card-head"><i class="fas fa-user"></i> Delivery Info</div>
      <div class="aod-card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="aod-info-row"><span class="aod-info-lbl">Full name</span><span class="aod-info-val"><?php echo e($order->customer_name); ?></span></div>
            <div class="aod-info-row"><span class="aod-info-lbl">Email</span><span class="aod-info-val" style="font-size:12px"><?php echo e($order->customer_email); ?></span></div>
            <div class="aod-info-row"><span class="aod-info-lbl">Phone</span><span class="aod-info-val"><?php echo e($order->customer_phone); ?></span></div>
          </div>
          <div class="col-md-6">
            <div class="aod-info-row"><span class="aod-info-lbl">Address</span><span class="aod-info-val" style="max-width:200px;text-align:right"><?php echo e($order->delivery_address); ?></span></div>
            <?php if($order->notes): ?>
            <div class="aod-info-row"><span class="aod-info-lbl">Notes</span><span class="aod-info-val"><?php echo e($order->notes); ?></span></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  
  <div>
    
    <?php if($st !== 'delivered' && $st !== 'cancelled'): ?>
    <div class="aod-card">
      <div class="aod-card-head"><i class="fas fa-bolt"></i> Update Status</div>
      <div class="aod-card-body">
        
        <?php if($st === 'pending'): ?>
        <form method="POST" action="<?php echo e(route('admin.orders.update',$order)); ?>">
          <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
          <input type="hidden" name="status" value="confirmed">
          <button type="submit" class="aod-btn-step aod-btn-confirm">
            <i class="fas fa-check"></i> Order Confirmation
          </button>
        </form>
        <?php endif; ?>
        <?php if($st === 'confirmed'): ?>
        <form method="POST" action="<?php echo e(route('admin.orders.update',$order)); ?>" id="shipForm">
          <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
          <input type="hidden" name="status" value="shipped">
          <div style="margin-bottom:10px">
            <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px">Tracking number <span style="color:#94a3b8">(optional)</span></label>
            <input type="text" name="tracking_number" value="<?php echo e($order->tracking_number); ?>"
                   placeholder="VD: GHTK1234567"
                   style="width:100%;padding:8px 12px;border:1.5px solid #e8edf2;border-radius:8px;font-size:13px">
          </div>
          <div style="margin-bottom:10px">
            <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px">Shipping carrier</label>
            <select name="shipping_provider" style="width:100%;padding:8px 12px;border:1.5px solid #e8edf2;border-radius:8px;font-size:13px">
              <option value="">-- Select --</option>
              <?php $__currentLoopData = ['GHTK','GHN','VNPT Post','Viettel Post','J&T Express','Shopee Express','TikiNOW']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($sp); ?>" <?php echo e($order->shipping_provider===$sp?'selected':''); ?>><?php echo e($sp); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <button type="submit" class="aod-btn-step aod-btn-ship">
            <i class="fas fa-truck"></i> Hand to carrier
          </button>
        </form>
        <?php endif; ?>
        <?php if($st === 'shipped'): ?>
        <form method="POST" action="<?php echo e(route('admin.orders.update',$order)); ?>">
          <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
          <input type="hidden" name="status" value="delivered">
          <button type="submit" class="aod-btn-step aod-btn-deliver">
            <i class="fas fa-home"></i> Confirm delivered
          </button>
        </form>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo e(route('admin.orders.update',$order)); ?>" onsubmit="return confirm('Cancel this order?')">
          <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
          <input type="hidden" name="status" value="cancelled">
          <button type="submit" class="aod-btn-step aod-btn-cancel" style="border:1.5px solid #e84040;background:#fff;color:#e84040;margin-top:6px">
            <i class="fas fa-times"></i> Cancel order
          </button>
        </form>
      </div>
    </div>
    <?php else: ?>
    <div class="aod-card">
      <div class="aod-card-body" style="text-align:center;padding:24px">
        <?php if($st === 'delivered'): ?>
          <div style="font-size:2rem">✅</div>
          <div style="font-weight:700;color:#00b894;margin-top:6px">Delivered</div>
          <div style="font-size:12px;color:#94a3b8"><?php echo e($upd->format('H:i, d/m/Y')); ?></div>
        <?php else: ?>
          <div style="font-size:2rem">❌</div>
          <div style="font-weight:700;color:#e84040;margin-top:6px">Order cancelled</div>
          <div style="font-size:12px;color:#94a3b8"><?php echo e($upd->format('H:i, d/m/Y')); ?></div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    
    <div class="aod-card">
      <div class="aod-card-head"><i class="fas fa-sliders-h"></i> Manual status update</div>
      <div class="aod-card-body">
        <form method="POST" action="<?php echo e(route('admin.orders.update',$order)); ?>">
          <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
          <select name="status" style="width:100%;padding:9px 12px;border:1.5px solid #e8edf2;border-radius:8px;font-size:13px;margin-bottom:10px">
            <option value="pending"   <?php echo e($st==='pending'?'selected':''); ?>>Pending confirmation</option>
            <option value="confirmed" <?php echo e($st==='confirmed'?'selected':''); ?>>Confirmed</option>
            <option value="shipped"   <?php echo e($st==='shipped'?'selected':''); ?>>Shipping</option>
            <option value="delivered" <?php echo e($st==='delivered'?'selected':''); ?>>Delivered</option>
            <option value="cancelled" <?php echo e($st==='cancelled'?'selected':''); ?>>Cancelled</option>
          </select>
          <button type="submit" style="width:100%;padding:9px;background:#f4f7fa;border:1.5px solid #e8edf2;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;color:#1a1f2e">
            <i class="fas fa-save me-1"></i> Save changes
          </button>
        </form>
      </div>
    </div>

    
    <div class="aod-card">
      <div class="aod-card-head"><i class="fas fa-credit-card"></i> Checkout</div>
      <div class="aod-card-body">
        <div class="aod-info-row">
          <span class="aod-info-lbl">Payment method</span>
          <span class="aod-info-val"><?php echo e($payLabels[$order->payment_method??''] ?? ucfirst($order->payment_method??'—')); ?></span>
        </div>
        <div class="aod-info-row">
          <span class="aod-info-lbl">Payment Status</span>
          <span class="aod-info-val">
            <?php if(in_array($order->payment_status,['paid','completed'])): ?>
              <span style="background:#e6faf5;color:#00b894;padding:3px 10px;border-radius:10px;font-size:12px;font-weight:700">✓ Paid</span>
            <?php else: ?>
              <span style="background:#fff9ec;color:#f39c12;padding:3px 10px;border-radius:10px;font-size:12px;font-weight:700">⏳ Awaiting payment</span>
            <?php endif; ?>
          </span>
        </div>
        <?php if($order->payment_reference): ?>
        <div class="aod-info-row">
          <span class="aod-info-lbl">Transaction ID</span>
          <span class="aod-info-val" style="font-size:12px"><?php echo e($order->payment_reference); ?></span>
        </div>
        <?php endif; ?>
        <?php if($order->paid_at): ?>
        <div class="aod-info-row">
          <span class="aod-info-lbl">Paid at</span>
          <span class="aod-info-val"><?php echo e(($order->paid_at instanceof \Carbon\Carbon ? $order->paid_at : \Carbon\Carbon::parse($order->paid_at))->format('H:i, d/m/Y')); ?></span>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\admin\orders\show.blade.php ENDPATH**/ ?>