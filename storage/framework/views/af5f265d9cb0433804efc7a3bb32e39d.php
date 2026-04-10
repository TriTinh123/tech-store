

<?php $__env->startSection('title', 'Users'); ?>

<?php $__env->startSection('body-content'); ?>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="pg-hdr">
    <div>
        <h2>Users</h2>
        <p>User Management</p>
    </div>
</div>


<?php
$usrCards = [
    ['id'=>'byDay',     'val'=>$totalUsers,   'lbl'=>'Total Users',    'icon'=>'fa-users',       'ibg'=>'#dbeafe','ic'=>'#1d4ed8','accentBg'=>'#eff6ff','accentBorder'=>'#93c5fd'],
    ['id'=>'active',    'val'=>$activeUsers,  'lbl'=>'Active',         'icon'=>'fa-user-check',  'ibg'=>'#dcfce7','ic'=>'#16a34a','accentBg'=>'#f0fdf4','accentBorder'=>'#86efac'],
    ['id'=>'blocked',   'val'=>$blockedUsers, 'lbl'=>'Blocked',        'icon'=>'fa-user-lock',   'ibg'=>'#fee2e2','ic'=>'#dc2626','accentBg'=>'#fef2f2','accentBorder'=>'#fca5a5'],
    ['id'=>'newWeek',   'val'=>$newThisWeek,  'lbl'=>'New This Week',  'icon'=>'fa-user-plus',   'ibg'=>'#fef3c7','ic'=>'#d97706','accentBg'=>'#fffbeb','accentBorder'=>'#fcd34d'],
];
?>
<div class="row g-3 mb-4">
    <?php $__currentLoopData = $usrCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-clickable" data-chart="<?php echo e($c['id']); ?>"
             style="cursor:pointer;transition:all .2s;border:1.5px solid transparent"
             onmouseenter="this.style.borderColor='<?php echo e($c['accentBorder']); ?>';this.style.background='<?php echo e($c['accentBg']); ?>';this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,.09)'"
             onmouseleave="this.style.borderColor='transparent';this.style.background='var(--card)';this.style.transform='';this.style.boxShadow='var(--shadow)'">
            <div class="stat-icon" style="background:<?php echo e($c['ibg']); ?>">
                <i class="fas <?php echo e($c['icon']); ?>" style="color:<?php echo e($c['ic']); ?>"></i>
            </div>
            <div style="flex:1">
                <div class="stat-val"><?php echo e($c['val']); ?></div>
                <div class="stat-lbl"><?php echo e($c['lbl']); ?></div>
            </div>
            <div style="font-size:11px;color:#94a3b8;align-self:flex-end;padding-bottom:2px"><i class="fas fa-chart-bar"></i></div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="modal fade" id="chartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border:none;border-radius:14px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.18)">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e293b,#0f172a);border:none;padding:16px 22px">
                <div style="flex:1">
                    <h5 class="modal-title" id="chartModalTitle" style="color:#fff;font-size:15px;font-weight:600;margin:0"></h5>
                    <p id="chartModalSub" style="color:#94a3b8;font-size:12px;margin:3px 0 0"></p>
                </div>
                <div style="display:flex;align-items:center;gap:8px">
                    <button id="chartDownloadBtn" title="Download as PNG"
                        style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;border-radius:8px;padding:5px 13px;font-size:12px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:6px"
                        onmouseenter="this.style.background='rgba(255,255,255,.22)'"
                        onmouseleave="this.style.background='rgba(255,255,255,.12)'">
                        <i class="fas fa-download" style="font-size:11px"></i> Save PNG
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body" style="padding:24px;background:#fff;min-height:300px;display:flex;flex-direction:column;align-items:center;justify-content:center">
                <canvas id="chartModalCanvas" style="max-height:280px;width:100%"></canvas>
                <p style="margin-top:10px;font-size:11px;color:#94a3b8"><i class="fas fa-mouse-pointer" style="margin-right:4px"></i>Right-click → <b>Copy image</b> or use <b>Save PNG</b></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <?php if($users && count($users) > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="color:var(--muted)"><?php echo e($user->id); ?></td>
                    <td><span style="font-weight:500"><?php echo e($user->name); ?></span></td>
                    <td style="color:var(--muted)"><?php echo e($user->email); ?></td>
                    <td>
                        <span class="bs <?php echo e($user->role === 'admin' ? 'bs-admin' : 'bs-user'); ?>">
                            <?php echo e($user->role ?? 'user'); ?>

                        </span>
                    </td>
                    <td>
                        <span class="bs <?php echo e($user->is_blocked ? 'bs-blocked' : 'bs-active'); ?>">
                            <?php echo e($user->is_blocked ? 'Blocked' : 'Active'); ?>

                        </span>
                    </td>
                    <td style="color:var(--muted)"><?php echo e($user->created_at?->format('d/m/Y')); ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <form action="<?php echo e(route('admin.users.toggle', $user->id)); ?>" method="POST" style="display:inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <button type="submit"
                                    class="btn-icon <?php echo e($user->is_blocked ? 'btn-icon-primary' : ''); ?>"
                                    title="<?php echo e($user->is_blocked ? 'Unblock' : 'Block'); ?>"
                                    style="<?php echo e(!$user->is_blocked ? 'color:var(--warning)' : ''); ?>">
                                    <i class="fas <?php echo e($user->is_blocked ? 'fa-lock-open' : 'fa-lock'); ?>"></i>
                                </button>
                            </form>
                            <form action="<?php echo e(route('admin.users.delete', $user->id)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Delete this user?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-icon btn-icon-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <p>No users found</p>
    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('extra-js'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function(){
    const whiteBgPlugin = {
        id:'whiteBg',
        beforeDraw(chart){ const {ctx}=chart; ctx.save(); ctx.globalCompositeOperation='destination-over'; ctx.fillStyle='#fff'; ctx.fillRect(0,0,chart.width,chart.height); ctx.restore(); }
    };
    const usersByDay  = <?php echo json_encode($usersByDay, 15, 512) ?>;
    const userStats   = <?php echo json_encode($userStats, 15, 512) ?>;
    const modal   = new bootstrap.Modal(document.getElementById('chartModal'));
    const titleEl = document.getElementById('chartModalTitle');
    const subEl   = document.getElementById('chartModalSub');
    let activeChart = null, currentKey = null;
    const chartDefs = {
        byDay:{
            title:'📅 New Users (Last 7 Days)', sub:'Daily user registrations over the past week',
            build(ctx){
                const labels=usersByDay.map(r=>r.day), data=usersByDay.map(r=>r.cnt);
                return new Chart(ctx,{type:'bar',data:{labels,datasets:[{label:'New Users',data,backgroundColor:'#3b82f6',borderRadius:6,borderSkipped:false}]},plugins:[whiteBgPlugin],options:{responsive:true,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.parsed.y} new users`}}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#f1f5f9'}}}}});
            }
        },
        active:{
            title:'✅ Active vs Blocked Users', sub:'Current account status distribution',
            build(ctx){
                return new Chart(ctx,{type:'doughnut',data:{labels:['Active','Blocked'],datasets:[{data:[userStats.active,userStats.blocked],backgroundColor:['#10b981','#ef4444'],borderWidth:3,borderColor:'#fff',hoverOffset:8}]},plugins:[whiteBgPlugin],options:{cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:16}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed}`}}}}});
            }
        },
        blocked:{
            title:'🔒 Blocked Users', sub:'Active vs blocked account ratio',
            build(ctx){
                return new Chart(ctx,{type:'doughnut',data:{labels:['Active','Blocked'],datasets:[{data:[userStats.active,userStats.blocked],backgroundColor:['#10b981','#ef4444'],borderWidth:3,borderColor:'#fff',hoverOffset:8}]},plugins:[whiteBgPlugin],options:{cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:16}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed}`}}}}});
            }
        },
        newWeek:{
            title:'🆕 New Users This Week', sub:'Registrations over the past 7 days',
            build(ctx){
                const labels=usersByDay.map(r=>r.day), data=usersByDay.map(r=>r.cnt);
                return new Chart(ctx,{type:'line',data:{labels,datasets:[{label:'New Users',data,borderColor:'#f59e0b',backgroundColor:'rgba(245,158,11,.15)',borderWidth:2,pointRadius:4,tension:0.3,fill:true}]},plugins:[whiteBgPlugin],options:{responsive:true,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.parsed.y} users`}}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#f1f5f9'}}}}});
            }
        },
    };
    document.querySelectorAll('.stat-clickable').forEach(el=>{
        el.addEventListener('click',()=>{
            const key=el.dataset.chart; if(!chartDefs[key]) return;
            currentKey=key; titleEl.textContent=chartDefs[key].title; subEl.textContent=chartDefs[key].sub;
            if(activeChart){activeChart.destroy();activeChart=null;}
            const wrap=document.querySelector('#chartModal .modal-body');
            wrap.innerHTML='<canvas id="chartModalCanvas" style="max-height:280px;width:100%"></canvas><p style="margin-top:10px;font-size:11px;color:#94a3b8"><i class="fas fa-mouse-pointer" style="margin-right:4px"></i>Right-click &rarr; <b>Copy image</b> or use <b>Save PNG</b></p>';
            activeChart=chartDefs[key].build(document.getElementById('chartModalCanvas').getContext('2d'));
            modal.show();
        });
    });
    document.getElementById('chartModal').addEventListener('hidden.bs.modal',()=>{if(activeChart){activeChart.destroy();activeChart=null;}});
    document.getElementById('chartDownloadBtn').addEventListener('click',function(){
        if(!activeChart) return;
        const c=document.getElementById('chartModalCanvas'),o=document.createElement('canvas');
        o.width=c.width;o.height=c.height;
        const oc=o.getContext('2d');oc.fillStyle='#fff';oc.fillRect(0,0,o.width,o.height);oc.drawImage(c,0,0);
        const a=document.createElement('a');a.href=o.toDataURL('image/png');a.download=(currentKey||'chart')+'-users.png';a.click();
    });
})();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/admin/users/index.blade.php ENDPATH**/ ?>