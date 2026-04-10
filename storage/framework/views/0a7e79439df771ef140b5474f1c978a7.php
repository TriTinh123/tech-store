
<?php $__env->startSection('title', 'Notifications — TechStore'); ?>
<?php $__env->startSection('page_title', 'My Notifications'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .nf-wrap { max-width:780px; margin:0 auto; padding:32px 20px 56px; }
    .nf-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
    .nf-title { font-size:18px; font-weight:800; color:#1a1f2e; display:flex; align-items:center; gap:10px; }
    .nf-title i { color:#0984e3; }
    .nf-mark-all { padding:8px 16px; background:linear-gradient(90deg,#00b894,#0984e3); color:white; border:none; border-radius:50px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit; transition:opacity .2s; }
    .nf-mark-all:hover { opacity:.85; }
    .nf-empty { background:white; border-radius:14px; padding:60px 20px; text-align:center; color:#94a3b8; box-shadow:0 2px 10px rgba(0,0,0,.06); border:1.5px solid #e8edf2; }
    .nf-empty i { font-size:52px; margin-bottom:14px; display:block; opacity:.3; }
    .nf-empty p { font-size:15px; margin:0; }
    .nf-list { background:white; border-radius:14px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,.06); border:1.5px solid #e8edf2; }
    .nf-item { display:flex; gap:14px; align-items:flex-start; padding:16px 20px; border-bottom:1px solid #f1f5f9; transition:background .2s; }
    .nf-item:last-child { border-bottom:none; }
    .nf-item.unread { background:#f0f9ff; }
    .nf-item:hover { background:#f8fafc; }
    .nf-ico { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:15px; }
    .nf-body { flex:1; min-width:0; }
    .nf-row { display:flex; align-items:center; gap:8px; margin-bottom:3px; }
    .nf-subject { font-size:13px; font-weight:600; color:#1a1f2e; }
    .nf-item.unread .nf-subject { font-weight:700; }
    .nf-new-badge { background:#0984e3; color:#fff; font-size:9px; padding:1px 6px; border-radius:999px; font-weight:700; }
    .nf-msg { font-size:12px; color:#64748b; margin:0 0 5px; line-height:1.6; }
    .nf-meta { display:flex; align-items:center; gap:12px; font-size:11px; color:#94a3b8; }
    .nf-meta a { color:#0984e3; text-decoration:none; font-weight:600; }
    .nf-meta a:hover { text-decoration:underline; }
    .nf-dot { width:8px; height:8px; background:#0984e3; border-radius:50%; flex-shrink:0; margin-top:5px; }
    .nf-pagination { margin-top:16px; display:flex; justify-content:center; }
</style>
<div class="nf-wrap">

    <div class="nf-header">
        <div class="nf-title"><i class="fas fa-bell"></i> Notifications</div>
        <?php if($unreadCount > 0): ?>
        <form method="POST" action="<?php echo e(route('notifications.read-all')); ?>" style="margin:0">
            <?php echo csrf_field(); ?>
            <button type="submit" class="nf-mark-all">
                <i class="fas fa-check-double"></i> Mark all as read (<?php echo e($unreadCount); ?>)
            </button>
        </form>
        <?php endif; ?>
    </div>

    <?php if($notifications->count() === 0): ?>
    <div class="nf-empty">
        <i class="fas fa-bell-slash"></i>
        <p>You have no notifications yet</p>
    </div>
    <?php else: ?>
    <?php
    $sevColor = ['info'=>'#0984e3','success'=>'#00b894','warning'=>'#f59e0b','danger'=>'#e84040','critical'=>'#7c3aed'];
    $sevBg    = ['info'=>'#dbeafe','success'=>'#d1fae5','warning'=>'#fef3c7','danger'=>'#fee2e2','critical'=>'#ede9fe'];
    $sevIcon  = ['info'=>'fa-info-circle','success'=>'fa-check-circle','warning'=>'fa-exclamation-triangle','danger'=>'fa-times-circle','critical'=>'fa-skull-crossbones'];
    ?>
    <div class="nf-list">
        <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $sev = $notif->severity ?? 'info'; $c = $sevColor[$sev] ?? '#0984e3'; $bg = $sevBg[$sev] ?? '#dbeafe'; ?>
        <div class="nf-item <?php echo e(!$notif->read ? 'unread' : ''); ?>">
            <div class="nf-ico" style="background:<?php echo e($bg); ?>">
                <i class="fas <?php echo e($sevIcon[$sev] ?? 'fa-bell'); ?>" style="color:<?php echo e($c); ?>"></i>
            </div>
            <div class="nf-body">
                <div class="nf-row">
                    <span class="nf-subject"><?php echo e($notif->title); ?></span>
                    <?php if(!$notif->read): ?><span class="nf-new-badge">NEW</span><?php endif; ?>
                </div>
                <p class="nf-msg"><?php echo e($notif->message); ?></p>
                <div class="nf-meta">
                    <span><i class="far fa-clock"></i> <?php echo e($notif->created_at->diffForHumans()); ?></span>
                    <?php if($notif->action_url): ?>
                    <a href="<?php echo e($notif->action_url); ?>"><?php echo e($notif->action_label ?? 'View'); ?> →</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php if(!$notif->read): ?>
            <div class="nf-dot"></div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="nf-pagination"><?php echo e($notifications->links()); ?></div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\notifications\index.blade.php ENDPATH**/ ?>