

<?php $__env->startSection('page_title', 'My Orders'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusCfg = [
        'pending'   => ['label' => 'Pending confirmation',    'color' => '#f39c12', 'bg' => '#fff9ec','icon' => 'fa-clock',       'pct' => 10],
        'confirmed' => ['label' => 'Confirmed',     'color' => '#0984e3', 'bg' => '#e8f4fd','icon' => 'fa-check',       'pct' => 35],
        'shipped'   => ['label' => 'Shipping',  'color' => '#6c5ce7', 'bg' => '#f0eeff','icon' => 'fa-truck',       'pct' => 70],
        'delivered' => ['label' => 'Delivered','color'=> '#00b894', 'bg' => '#e6faf5','icon' => 'fa-check-circle','pct' => 100],
        'cancelled' => ['label' => 'Cancelled',      'color' => '#e84040', 'bg' => '#ffeaea','icon' => 'fa-times-circle','pct' => 0],
    ];
?>

<div class="oh-wrap">

  
  <div class="oh-topbar">
    <div>
      <h1 class="oh-page-title"><i class="fas fa-shopping-bag"></i> My Orders</h1>
      <p class="oh-page-sub">Track and manage all your orders</p>
    </div>
    <a href="<?php echo e(route('home')); ?>" class="oh-btn-shop"><i class="fas fa-plus"></i> Shop more</a>
  </div>

  <?php if(session('success')): ?>
  <div class="oh-alert-ok"><i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <?php if($orders->count() == 0): ?>
  <div class="oh-empty">
    <div class="oh-empty-icon"><i class="fas fa-box-open"></i></div>
    <div class="oh-empty-title">No orders yet</div>
    <div class="oh-empty-sub">Explore and place your first order!</div>
    <a href="<?php echo e(route('home')); ?>" class="oh-btn-primary"><i class="fas fa-shopping-cart"></i> Shop now</a>
  </div>
  <?php else: ?>

  
  <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php $sc = $statusCfg[$order->status] ?? $statusCfg['pending']; ?>
  <div class="oh-order-card">
    
    <div class="oh-card-head">
      <div class="oh-card-meta">
        <span class="oh-order-num">#<?php echo e($order->order_number ?? $order->id); ?></span>
        <span class="oh-order-date"><i class="fas fa-calendar-alt"></i> <?php echo e($order->created_at->format('d/m/Y H:i')); ?></span>
        <span class="oh-order-count"><i class="fas fa-box"></i> <?php echo e($order->items_count ?? $order->items()->count()); ?> products</span>
      </div>
      <span class="oh-status-badge" style="background:<?php echo e($sc['bg']); ?>;color:<?php echo e($sc['color']); ?>;border:1px solid <?php echo e($sc['color']); ?>33">
        <i class="fas <?php echo e($sc['icon']); ?>"></i> <?php echo e($sc['label']); ?>

      </span>
    </div>

    
    <?php if($order->status !== 'cancelled'): ?>
    <div class="oh-progress-strip">
      <div class="oh-ps-track">
        <div class="oh-ps-fill" style="width:<?php echo e($sc['pct']); ?>%;background:<?php echo e($sc['color']); ?>"></div>
        <?php $__currentLoopData = ['Place Order','Confirm','Shipping','Received']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pi => $pl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $pPct = [0=>0,1=>33,2=>66,3=>100][$pi]; $done = $sc['pct'] >= $pPct+5; ?>
          <div class="oh-ps-node <?php echo e($done ? 'done' : ''); ?>" style="<?php echo e($done ? 'background:'.$sc['color'] : ''); ?>">
            <?php echo e($done ? '✓' : ($pi + 1)); ?>

          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div class="oh-ps-labels">
        <?php $__currentLoopData = ['Place Order','Confirm','Shipping','Received']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <span><?php echo e($pl); ?></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
    <?php else: ?>
    <div class="oh-cancelled-strip">
      <i class="fas fa-ban"></i> Order cancelled on <?php echo e($order->updated_at->format('d/m/Y H:i')); ?>

    </div>
    <?php endif; ?>

    
    <div class="oh-card-body">
      
      <div class="oh-items-preview">
        <?php $__currentLoopData = $order->items->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="oh-preview-item">
          <?php if(isset($item->product->image)): ?>
          <img src="<?php echo e(asset('storage/'.$item->product->image)); ?>" class="oh-item-thumb"
               onerror="this.src='<?php echo e(asset('images/no-image.svg')); ?>'" alt="">
          <?php else: ?>
          <div class="oh-item-thumb-ph"><i class="fas fa-image"></i></div>
          <?php endif; ?>
          <span class="oh-item-name"><?php echo e(Str::limit($item->product->name ?? 'Products', 30)); ?></span>
          <span class="oh-item-qty">x<?php echo e($item->quantity); ?></span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($order->items->count() > 2): ?>
        <div class="oh-more-items">+<?php echo e($order->items->count() - 2); ?> more products</div>
        <?php endif; ?>
      </div>

      
      <div class="oh-card-right">
        <?php if($order->status !== 'delivered'): ?>
        <div class="oh-eta">
          <i class="fas fa-clock"></i>
          <?php if($order->status === 'cancelled'): ?> Cancelled
          <?php elseif($order->status === 'shipped'): ?> Est. <?php echo e($order->created_at->addDays(3)->format('d/m/Y')); ?>

          <?php else: ?> In progress
          <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="oh-eta delivered"><i class="fas fa-check-circle"></i> Delivered <?php echo e($order->updated_at->format('d/m/Y')); ?></div>
        <?php endif; ?>
        <div class="oh-total">$<?php echo e(number_format($order->total_amount, 2)); ?></div>
        <a href="<?php echo e(route('profile.order-detail', $order->id)); ?>" class="oh-btn-detail" style="background:<?php echo e($sc['color']); ?>">
          <i class="fas fa-map-marker-alt"></i> Track Order
        </a>
      </div>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  
  <?php if($orders->hasPages()): ?>
  <div class="oh-pagination">
    <?php if($orders->onFirstPage()): ?>
      <span class="oh-page-btn disabled">← Previous</span>
    <?php else: ?>
      <a class="oh-page-btn" href="<?php echo e($orders->previousPageUrl()); ?>">← Previous</a>
    <?php endif; ?>
    <?php $__currentLoopData = $orders->getUrlRange(1, $orders->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php if($page == $orders->currentPage()): ?>
        <span class="oh-page-btn active"><?php echo e($page); ?></span>
      <?php else: ?>
        <a class="oh-page-btn" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
      <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if($orders->hasMorePages()): ?>
      <a class="oh-page-btn" href="<?php echo e($orders->nextPageUrl()); ?>">Next →</a>
    <?php else: ?>
      <span class="oh-page-btn disabled">Next →</span>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php endif; ?>
</div>

<style>
:root{--green:#00b894;--blue:#0984e3;--text:#1a1f2e;--text-m:#64748b;--border:#e8edf2;--bg:#f4f7fa}
.oh-wrap{max-width:900px;margin:32px auto;padding:0 16px}

/* Top bar */
.oh-topbar{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;gap:12px;flex-wrap:wrap}
.oh-page-title{font-size:1.5rem;font-weight:800;color:var(--text);margin:0;display:flex;align-items:center;gap:10px}
.oh-page-title i{color:var(--green)}
.oh-page-sub{font-size:.9rem;color:var(--text-m);margin:4px 0 0}
.oh-btn-shop{background:var(--green);color:#fff;padding:10px 20px;border-radius:10px;font-weight:600;text-decoration:none;font-size:.9rem;display:inline-flex;align-items:center;gap:8px;transition:background .2s}
.oh-btn-shop:hover{background:#00a381;color:#fff}

/* Alert */
.oh-alert-ok{background:#e6faf5;border:1px solid #00b89433;color:var(--green);padding:12px 18px;border-radius:10px;margin-bottom:20px;font-weight:600;display:flex;align-items:center;gap:8px}

/* Empty */
.oh-empty{background:#fff;border-radius:16px;padding:60px 24px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.06)}
.oh-empty-icon{font-size:3.5rem;color:#e0e7ef;margin-bottom:16px}
.oh-empty-title{font-size:1.2rem;font-weight:700;color:var(--text);margin-bottom:6px}
.oh-empty-sub{color:var(--text-m);margin-bottom:24px}
.oh-btn-primary{background:var(--green);color:#fff;padding:12px 28px;border-radius:10px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px}

/* Order card */
.oh-order-card{background:#fff;border-radius:16px;margin-bottom:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden;transition:box-shadow .2s}
.oh-order-card:hover{box-shadow:0 6px 24px rgba(0,0,0,.1)}

/* Card head */
.oh-card-head{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border);gap:12px;flex-wrap:wrap}
.oh-card-meta{display:flex;align-items:center;gap:14px;flex-wrap:wrap}
.oh-order-num{font-weight:800;color:var(--text);font-size:1rem}
.oh-order-date,.oh-order-count{font-size:.82rem;color:var(--text-m);display:flex;align-items:center;gap:4px}
.oh-status-badge{font-size:.8rem;font-weight:700;padding:5px 12px;border-radius:20px;display:inline-flex;align-items:center;gap:5px;white-space:nowrap}

/* Progress strip */
.oh-progress-strip{padding:16px 20px 8px;background:#fafbfc}
.oh-ps-track{position:relative;display:flex;align-items:center;justify-content:space-between;height:28px}
.oh-ps-track::before{content:'';position:absolute;top:50%;transform:translateY(-50%);left:14px;right:14px;height:4px;background:var(--border);border-radius:2px;z-index:0}
.oh-ps-fill{position:absolute;top:50%;transform:translateY(-50%);left:14px;height:4px;border-radius:2px;z-index:1;transition:width .5s ease;min-width:4px}
.oh-ps-node{width:28px;height:28px;border-radius:50%;border:3px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#b2bec3;z-index:2;position:relative;transition:all .3s}
.oh-ps-node.done{color:#fff;border-color:transparent}
.oh-ps-labels{display:flex;justify-content:space-between;margin-top:6px;padding:0 6px}
.oh-ps-labels span{font-size:.72rem;color:var(--text-m);text-align:center;flex:1}

/* Cancelled strip */
.oh-cancelled-strip{background:#ffeaea;color:#e84040;padding:10px 20px;font-size:.85rem;font-weight:600;display:flex;align-items:center;gap:8px}

/* Card body */
.oh-card-body{display:flex;align-items:flex-end;justify-content:space-between;padding:16px 20px;gap:16px;flex-wrap:wrap}
.oh-items-preview{flex:1;display:flex;flex-direction:column;gap:8px}
.oh-preview-item{display:flex;align-items:center;gap:10px}
.oh-item-thumb{width:44px;height:44px;object-fit:cover;border-radius:8px;flex-shrink:0}
.oh-item-thumb-ph{width:44px;height:44px;background:var(--bg);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#b2bec3;flex-shrink:0}
.oh-item-name{font-size:.85rem;color:var(--text);flex:1}
.oh-item-qty{font-size:.8rem;color:var(--text-m);white-space:nowrap}
.oh-more-items{font-size:.8rem;color:var(--text-m);margin-left:54px}

/* Card right */
.oh-card-right{display:flex;flex-direction:column;align-items:flex-end;gap:10px;flex-shrink:0}
.oh-eta{font-size:.8rem;color:var(--text-m);display:flex;align-items:center;gap:5px}
.oh-eta.delivered{color:var(--green);font-weight:600}
.oh-total{font-size:1.15rem;font-weight:800;color:var(--text)}
.oh-btn-detail{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;color:#fff;font-size:.85rem;font-weight:700;text-decoration:none;transition:opacity .2s}
.oh-btn-detail:hover{opacity:.85;color:#fff}

/* Pagination */
.oh-pagination{display:flex;justify-content:center;gap:6px;flex-wrap:wrap;margin-top:28px}
.oh-page-btn{padding:8px 14px;border-radius:8px;background:#fff;border:1px solid var(--border);color:var(--text-m);font-size:.88rem;text-decoration:none;transition:all .2s}
.oh-page-btn:hover{background:var(--green);color:#fff;border-color:var(--green)}
.oh-page-btn.active{background:var(--green);color:#fff;border-color:var(--green);font-weight:700}
.oh-page-btn.disabled{opacity:.45;cursor:default;pointer-events:none}

@media(max-width:600px){
  .oh-card-body{flex-direction:column;align-items:flex-start}
  .oh-card-right{align-items:flex-start;width:100%}
  .oh-btn-detail{width:100%;justify-content:center}
  .oh-card-head{flex-direction:column;align-items:flex-start}
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\profile\order-history.blade.php ENDPATH**/ ?>