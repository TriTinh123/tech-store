
<?php $__env->startSection('title', 'Order Management'); ?>

<?php $__env->startSection('body-content'); ?>
<?php
    $statusCfg = [
        'pending'   => ['label'=>'Pending confirmation', 'color'=>'#f39c12','bg'=>'#fff9ec','icon'=>'fa-clock'],
        'confirmed' => ['label'=>'Confirmed',    'color'=>'#0984e3','bg'=>'#e8f4fd','icon'=>'fa-check'],
        'shipped'   => ['label'=>'Shipping', 'color'=>'#6c5ce7','bg'=>'#f0eeff','icon'=>'fa-truck'],
        'delivered' => ['label'=>'Delivered',         'color'=>'#00b894','bg'=>'#e6faf5','icon'=>'fa-check-circle'],
        'cancelled' => ['label'=>'Cancelled',          'color'=>'#e84040','bg'=>'#ffeaea','icon'=>'fa-times-circle'],
    ];
    $allCount = array_sum($counts);
?>
<style>
.ao-tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:20px;}
.ao-tab{padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;border:1.5px solid #e8edf2;color:#64748b;background:#fff;transition:.15s;display:flex;align-items:center;gap:6px;}
.ao-tab:hover{border-color:#0984e3;color:#0984e3;}
.ao-tab.active{background:#0984e3;border-color:#0984e3;color:#fff;}
.ao-tab .cnt{font-size:11px;background:rgba(0,0,0,.12);padding:1px 6px;border-radius:10px;}
.ao-tab.active .cnt{background:rgba(255,255,255,.25);}
.ao-search-row{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center;}
.ao-search-row input{flex:1;min-width:180px;padding:9px 14px;border:1.5px solid #e8edf2;border-radius:8px;font-size:13px;background:#fff;}
.ao-search-row input:focus{outline:none;border-color:#0984e3;}
.ao-search-row button{padding:9px 18px;background:#0984e3;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;}
.ao-hbar{position:relative;height:6px;background:#e8edf2;border-radius:3px;overflow:hidden;margin-top:4px;}
.ao-hbar-fill{position:absolute;top:0;left:0;height:100%;border-radius:3px;transition:width .4s;}
.ao-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:12px;font-size:11.5px;font-weight:700;}
.ao-btn-confirm{padding:5px 12px;background:#00b894;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;}
.ao-btn-confirm:hover{background:#00a381;}
.ao-btn-ship{padding:5px 12px;background:#6c5ce7;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;}
.ao-btn-ship:hover{background:#5a4bd1;}
</style>

<div class="pg-hdr">
  <div>
    <h2>Order Management</h2>
    <p>Track, confirm and process orders</p>
  </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px;font-size:13px">
  <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>


<div class="ao-tabs">
  <a href="<?php echo e(route('admin.orders', ['search'=>$search])); ?>" class="ao-tab <?php echo e(!$status || $status==='all' ? 'active' : ''); ?>">
    All <span class="cnt"><?php echo e($allCount); ?></span>
  </a>
  <?php $__currentLoopData = $statusCfg; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sv => $sc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <a href="<?php echo e(route('admin.orders', ['status'=>$sv,'search'=>$search])); ?>"
     class="ao-tab <?php echo e($status===$sv ? 'active' : ''); ?>"
     style="<?php echo e($status===$sv ? '' : 'border-color:'.$sc['color'].'44;color:'.$sc['color']); ?>">
    <i class="fas <?php echo e($sc['icon']); ?>" style="font-size:11px"></i>
    <?php echo e($sc['label']); ?> <span class="cnt"><?php echo e($counts[$sv] ?? 0); ?></span>
  </a>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<form method="GET" action="<?php echo e(route('admin.orders')); ?>" class="ao-search-row">
  <?php if($status): ?><input type="hidden" name="status" value="<?php echo e($status); ?>"><?php endif; ?>
  <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search by order code, name, phone, email...">
  <button type="submit"><i class="fas fa-search me-1"></i>Search</button>
  <?php if($search): ?><a href="<?php echo e(route('admin.orders',['status'=>$status])); ?>" class="btn-icon btn-icon-danger" title="Clear search"><i class="fas fa-times"></i></a><?php endif; ?>
</form>

<div class="card">
  <?php if($orders->count() > 0): ?>
  <div class="table-responsive">
    <table class="table table-hover" style="font-size:13px">
      <thead>
        <tr>
          <th style="width:120px">Order ID</th>
          <th>Customer</th>
          <th style="width:130px">Total</th>
          <th style="width:95px">Status</th>
          <th style="width:110px">Payment</th>
          <th style="width:110px">Progress</th>
          <th style="width:130px">Order Date</th>
          <th style="width:180px">Quick Actions</th>
          <th style="width:50px"></th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $st   = $order->status ?? 'pending';
          $sc   = $statusCfg[$st] ?? $statusCfg['pending'];
          $pct  = ['pending'=>8,'confirmed'=>35,'shipped'=>68,'delivered'=>100,'cancelled'=>0][$st] ?? 8;
          $next = ['pending'=>'confirmed','confirmed'=>'shipped','shipped'=>'delivered'][$st] ?? null;
          $nextLabel = ['confirmed'=>'Confirm','shipped'=>'Shipping','delivered'=>'Delivered'][$next] ?? null;
        ?>
        <tr>
          <td>
            <div style="font-weight:700;color:#0984e3">#<?php echo e($order->order_number ?? $order->id); ?></div>
            <div style="font-size:11px;color:#94a3b8"><?php echo e($order->items()->count()); ?> items</div>
          </td>
          <td>
            <div style="font-weight:600"><?php echo e($order->customer_name ?? $order->user?->name); ?></div>
            <div style="font-size:11.5px;color:#94a3b8"><?php echo e($order->customer_phone); ?></div>
            <div style="font-size:11.5px;color:#94a3b8"><?php echo e($order->customer_email); ?></div>
          </td>
          <td style="font-weight:700;color:#e84040">$<?php echo e(number_format($order->total_amount, 2)); ?></td>
          <td>
            <span class="ao-badge" style="background:<?php echo e($sc['bg']); ?>;color:<?php echo e($sc['color']); ?>">
              <i class="fas <?php echo e($sc['icon']); ?>" style="font-size:10px"></i>
              <?php echo e($sc['label']); ?>

            </span>
          </td>
          <td>
            <?php if($order->payment_status === 'paid' || $order->payment_status === 'completed'): ?>
              <span class="ao-badge" style="background:#e6faf5;color:#00b894">✓ Paid</span>
            <?php else: ?>
              <span class="ao-badge" style="background:#fff9ec;color:#f39c12">⏳ Awaiting payment</span>
            <?php endif; ?>
          </td>
          <td style="width:110px">
            <div style="font-size:10.5px;color:#94a3b8;margin-bottom:3px"><?php echo e($pct); ?>%</div>
            <div class="ao-hbar">
              <div class="ao-hbar-fill" style="width:<?php echo e($pct); ?>%;background:<?php echo e($st==='cancelled' ? '#e84040' : ($pct===100 ? '#00b894' : '#0984e3')); ?>"></div>
            </div>
          </td>
          <td style="color:#64748b"><?php echo e($order->created_at?->format('d/m/Y')); ?><br><span style="font-size:11px"><?php echo e($order->created_at?->format('H:i')); ?></span></td>
          <td>
            <?php if($next): ?>
            <form method="POST" action="<?php echo e(route('admin.orders.update',$order)); ?>" style="display:inline">
              <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
              <input type="hidden" name="status" value="<?php echo e($next); ?>">
              <button type="submit" class="<?php echo e($next==='shipped' ? 'ao-btn-ship' : 'ao-btn-confirm'); ?>">
                <i class="fas <?php echo e($next==='shipped' ? 'fa-truck' : 'fa-check'); ?> me-1"></i><?php echo e($nextLabel); ?>

              </button>
            </form>
            <?php elseif($st === 'delivered'): ?>
              <span style="font-size:12px;color:#00b894;font-weight:600">✅ Completed</span>
            <?php elseif($st === 'cancelled'): ?>
              <span style="font-size:12px;color:#e84040">❌ Cancelled</span>
            <?php endif; ?>
          </td>
          <td>
            <a href="<?php echo e(route('admin.orders.show',$order)); ?>" class="btn-icon btn-icon-primary" title="View details">
              <i class="fas fa-eye"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>

  
  <?php if($orders->hasPages()): ?>
  <div style="padding:16px 20px;border-top:1px solid #e8edf2;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
    <div style="font-size:13px;color:#64748b">
      Show <?php echo e($orders->firstItem()); ?>–<?php echo e($orders->lastItem()); ?> / <?php echo e($orders->total()); ?> orders
    </div>
    <div style="display:flex;gap:6px">
      <?php if($orders->onFirstPage()): ?>
        <span style="padding:6px 12px;border-radius:6px;background:#f4f7fa;color:#b2bec3;font-size:13px">←</span>
      <?php else: ?>
        <a href="<?php echo e($orders->previousPageUrl()); ?>" style="padding:6px 12px;border-radius:6px;background:#f4f7fa;color:#64748b;font-size:13px;text-decoration:none">←</a>
      <?php endif; ?>
      <?php $__currentLoopData = $orders->getUrlRange(max(1,$orders->currentPage()-2), min($orders->lastPage(),$orders->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pg => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($url); ?>" style="padding:6px 12px;border-radius:6px;font-size:13px;text-decoration:none;<?php echo e($pg==$orders->currentPage() ? 'background:#0984e3;color:#fff' : 'background:#f4f7fa;color:#64748b'); ?>"><?php echo e($pg); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php if($orders->hasMorePages()): ?>
        <a href="<?php echo e($orders->nextPageUrl()); ?>" style="padding:6px 12px;border-radius:6px;background:#f4f7fa;color:#64748b;font-size:13px;text-decoration:none">→</a>
      <?php else: ?>
        <span style="padding:6px 12px;border-radius:6px;background:#f4f7fa;color:#b2bec3;font-size:13px">→</span>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php else: ?>
  <div class="empty-state">
    <i class="fas fa-shopping-bag"></i>
    <p>No orders found<?php echo e($search ? ' matching your search' : ''); ?></p>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>