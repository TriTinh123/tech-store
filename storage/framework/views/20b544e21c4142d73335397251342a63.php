

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('body-content'); ?>


<?php
$dashCards = [
    ['id'=>'revenue',  'val'=>'$'.number_format($totalRevenue??0,2), 'lbl'=>'Revenue',          'icon'=>'fa-chart-line',  'ibg'=>'#dcfce7','ic'=>'#16a34a','accentBg'=>'#f0fdf4','accentBorder'=>'#86efac'],
    ['id'=>'orders',   'val'=>$totalOrders??0,                                'lbl'=>'Orders',        'icon'=>'fa-shopping-bag','ibg'=>'#dbeafe','ic'=>'#1d4ed8','accentBg'=>'#eff6ff','accentBorder'=>'#93c5fd'],
    ['id'=>'products', 'val'=>$totalProducts??0,                              'lbl'=>'Products',        'icon'=>'fa-box',         'ibg'=>'#fef3c7','ic'=>'#d97706','accentBg'=>'#fffbeb','accentBorder'=>'#fcd34d'],
    ['id'=>'users',    'val'=>$totalUsers??0,                                 'lbl'=>'Users',      'icon'=>'fa-users',       'ibg'=>'#ede9fe','ic'=>'#7c3aed','accentBg'=>'#f5f3ff','accentBorder'=>'#c4b5fd'],
    ['id'=>'reviews',  'val'=>($totalReviews??0).' ('.($avgRating??0).'★)', 'lbl'=>'Product Rating',   'icon'=>'fa-star',        'ibg'=>'#fef9c3','ic'=>'#ca8a04','accentBg'=>'#fefce8','accentBorder'=>'#fde047'],
];
?>
<div class="row g-3 mb-4">
    <?php $__currentLoopData = $dashCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-6 col-xl">
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
            <div style="font-size:11px;color:#94a3b8;align-self:flex-end;padding-bottom:2px">
                <i class="fas fa-chart-bar"></i>
            </div>
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
                        style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;border-radius:8px;padding:5px 13px;font-size:12px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background .15s"
                        onmouseenter="this.style.background='rgba(255,255,255,.22)'"
                        onmouseleave="this.style.background='rgba(255,255,255,.12)'">
                        <i class="fas fa-download" style="font-size:11px"></i> Save PNG
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body" style="padding:24px;background:#fff;min-height:320px;display:flex;flex-direction:column;align-items:center;justify-content:center">
                <canvas id="chartModalCanvas" style="max-height:300px;width:100%"></canvas>
                <p style="margin-top:10px;font-size:11px;color:#94a3b8"><i class="fas fa-mouse-pointer" style="margin-right:4px"></i>Right-click the chart → <b>Copy image</b> or use <b>Save PNG</b> button above</p>
            </div>
        </div>
    </div>
</div>


<div class="row g-3">

    
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6>Recent Orders</h6>
                <a href="<?php echo e(route('admin.orders')); ?>" class="btn btn-sm btn-outline-secondary" style="font-size:12px">View all</a>
            </div>
            <?php if($recentOrders && count($recentOrders) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><strong>#<?php echo e($order->id); ?></strong></td>
                            <td><?php echo e($order->user?->name ?? 'Guest'); ?></td>
                            <td>$<?php echo e(number_format($order->total_amount ?? 0, 2)); ?></td>
                            <td>
                                <?php $st = strtolower($order->status ?? 'pending'); ?>
                                <span class="bs bs-<?php echo e($st); ?>"><?php echo e($order->status ?? 'Pending'); ?></span>
                            </td>
                            <td style="color:var(--muted)"><?php echo e($order->created_at?->format('d/m/Y')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No orders yet</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6>🏆 Best Sellers</h6>
            </div>
            <?php if($topProducts && count($topProducts) > 0): ?>
            <div class="card-body p-0">
                <?php $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="d-flex align-items-center gap-3 px-3 py-2 <?php echo e($i + 1 < count($topProducts) ? 'border-bottom' : ''); ?>" style="border-color:#f1f5f9!important">
                    <div style="width:28px;height:28px;border-radius:7px;background:linear-gradient(135deg,#3b82f6,#06b6d4);display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0">
                        <?php echo e($i + 1); ?>

                    </div>
                    <div class="flex-1 min-w-0" style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:500;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?php echo e($product->name); ?></div>
                        <div style="font-size:11px;color:var(--muted)">$<?php echo e(number_format($product->price ?? 0, 2)); ?> • Sold: <?php echo e((int)($product->sold_count ?? 0)); ?></div>
                    </div>
                    <?php if(($product->rating ?? 0) > 0): ?>
                    <div style="font-size:11px;color:#f59e0b;flex-shrink:0">
                        ★ <?php echo e(number_format($product->rating, 1)); ?>

                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-box"></i>
                <p>No products yet</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>


<div class="row g-3 mt-1">

    
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0">⭐ Rating Distribution</h6>
                <span style="font-size:12px;color:var(--muted)"><?php echo e($avgRating ?? 0); ?>★ average</span>
            </div>
            <div class="card-body">
                <?php $maxRating = max(array_values($ratingDistFull ?? [1])); if($maxRating == 0) $maxRating = 1; ?>
                <?php $__currentLoopData = $ratingDistFull; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $star => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
                    <span style="font-size:12px;font-weight:600;color:var(--text);width:14px;text-align:right"><?php echo e($star); ?></span>
                    <i class="fas fa-star" style="color:#f59e0b;font-size:11px;flex-shrink:0"></i>
                    <div style="flex:1;height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden">
                        <div style="height:100%;width:<?php echo e($maxRating > 0 ? round($count/$maxRating*100) : 0); ?>%;background:<?php echo e($star >= 4 ? '#22c55e' : ($star == 3 ? '#f59e0b' : '#ef4444')); ?>;border-radius:4px;transition:width .4s"></div>
                    </div>
                    <span style="font-size:11px;color:var(--muted);width:28px;text-align:right"><?php echo e($count); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div style="margin-top:14px;padding-top:12px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:12px;color:var(--muted)">Total</span>
                    <span style="font-size:14px;font-weight:700;color:var(--text)"><?php echo e($totalReviews ?? 0); ?> reviews</span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0">📈 Rating Last 7 Days</h6>
            </div>
            <div class="card-body" style="display:flex;align-items:center;justify-content:center;min-height:180px">
                <canvas id="reviewsByDayChart" style="max-height:200px;width:100%"></canvas>
            </div>
        </div>
    </div>

    
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0">💬 Latest Reviews</h6>
                <a href="<?php echo e(route('admin.reviews')); ?>" style="font-size:12px;color:#3b82f6;text-decoration:none">View all →</a>
            </div>
            <?php if(isset($recentReviews) && $recentReviews->count() > 0): ?>
            <div class="card-body p-0">
                <?php $__currentLoopData = $recentReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="padding:10px 16px;border-bottom:1px solid #f1f5f9;display:flex;gap:10px;align-items:flex-start">
                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0">
                        <?php echo e(strtoupper(substr($rv->user_name ?? 'U', 0, 1))); ?>

                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <span style="font-size:12px;font-weight:600;color:var(--text)"><?php echo e(Str::limit($rv->user_name, 14)); ?></span>
                            <span style="color:#f59e0b;font-size:11px"><?php echo e(str_repeat('★', $rv->rating)); ?><?php echo e(str_repeat('☆', 5 - $rv->rating)); ?></span>
                        </div>
                        <div style="font-size:11px;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            <?php echo e(Str::limit($rv->product?->name ?? '—', 22)); ?>

                        </div>
                        <?php if($rv->comment): ?>
                        <div style="font-size:11px;color:#64748b;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-style:italic">"<?php echo e(Str::limit($rv->comment, 36)); ?>"</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-star"></i><p>No reviews</p></div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('extra-js'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function(){
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#64748b';

    const revenueByDay       = <?php echo json_encode($revenueByDay, 15, 512) ?>;
    const ordersByStatus     = <?php echo json_encode($ordersByStatus, 15, 512) ?>;
    const productsByCategory = <?php echo json_encode($productsByCategory, 15, 512) ?>;
    const usersByDay         = <?php echo json_encode($usersByDay, 15, 512) ?>;
    const reviewsByDay       = <?php echo json_encode($reviewsByDay, 15, 512) ?>;
    const totalRevenue       = <?php echo e($totalRevenue ?? 0); ?>;

    // Build a full 7-day label array
    function last7Labels(){
        const labels = [];
        for(let i=6;i>=0;i--){
            const d = new Date(); d.setDate(d.getDate()-i);
            labels.push(d.getDate()+'/'+(d.getMonth()+1));
        }
        return labels;
    }
    function last7Key(i){
        const d = new Date(); d.setDate(d.getDate()-(6-i));
        return d.toISOString().slice(0,10);
    }
    function mapToLast7(arr, valKey){
        return last7Labels().map((_,i)=>{
            const key = last7Key(i);
            const row = arr.find(r=>r.day===key);
            return row ? row[valKey] : 0;
        });
    }

    // ── White-background plugin so right-click → Copy image / Save image looks clean
    const whiteBgPlugin = {
        id: 'whiteBg',
        beforeDraw(chart){
            const {ctx, chartArea} = chart;
            ctx.save();
            ctx.globalCompositeOperation = 'destination-over';
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, chart.width, chart.height);
            ctx.restore();
        }
    };

    const modal   = new bootstrap.Modal(document.getElementById('chartModal'));
    const titleEl = document.getElementById('chartModalTitle');
    const subEl   = document.getElementById('chartModalSub');
    let activeChart = null;
    let currentChartKey = null;

    // ── Download button
    document.getElementById('chartDownloadBtn').addEventListener('click', function(){
        if(!activeChart) return;
        const canvas = document.getElementById('chartModalCanvas');
        // Draw onto a fresh canvas with white bg to ensure clean export
        const offscreen = document.createElement('canvas');
        offscreen.width  = canvas.width;
        offscreen.height = canvas.height;
        const octx = offscreen.getContext('2d');
        octx.fillStyle = '#ffffff';
        octx.fillRect(0, 0, offscreen.width, offscreen.height);
        octx.drawImage(canvas, 0, 0);
        const link = document.createElement('a');
        link.href = offscreen.toDataURL('image/png');
        link.download = (currentChartKey || 'chart') + '-' + new Date().toISOString().slice(0,10) + '.png';
        link.click();
    });

    const statusColors = {
        pending:'#f59e0b', processing:'#3b82f6',
        shipped:'#06b6d4', delivered:'#10b981',
        cancelled:'#ef4444'
    };

    const chartDefs = {
        revenue: {
            title: '💰 Revenue Last 7 Days',
            sub:   'Total Revenue per day over the past week',
            build(ctx){
                const labels = last7Labels();
                const data   = mapToLast7(revenueByDay, 'revenue');
                return new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets:[{
                            label: 'Revenue (USD)',
                            data,
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22,163,74,.12)',
                            pointBackgroundColor: '#16a34a',
                            pointRadius: 5,
                            tension: 0.35,
                            fill: true,
                        }]
                    },
                    plugins: [whiteBgPlugin],
                    options:{
                        responsive: true,
                        plugins:{
                            legend:{display:false},
                            tooltip:{callbacks:{label:c=>`$${ Number(c.parsed.y).toFixed(2)}`}}
                        },
                        scales:{
                            x:{grid:{display:false}},
                            y:{beginAtZero:true,grid:{color:'#f1f5f9'},ticks:{callback:v=>Number(v/1000).toFixed(0)+'k'}}
                        }
                    }
                });
            }
        },
        orders: {
            title: '🛒 Order Distribution by Status',
            sub:   'All orders sorted by current status',
            build(ctx){
                const labels = Object.keys(ordersByStatus);
                const data   = Object.values(ordersByStatus);
                const colors = labels.map(l=>statusColors[l.toLowerCase()]||'#94a3b8');
                return new Chart(ctx, {
                    type: 'doughnut',
                    data:{
                        labels: labels.map(l=>l.charAt(0).toUpperCase()+l.slice(1)),
                        datasets:[{
                            data,
                            backgroundColor: colors,
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverOffset: 8,
                        }]
                    },
                    plugins: [whiteBgPlugin],
                    options:{
                        cutout: '60%',
                        plugins:{
                            legend:{position:'bottom',labels:{boxWidth:12,padding:16}},
                            tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed} orders`}}
                        }
                    }
                });
            }
        },
        products: {
            title: '📦 Products by Category',
            sub:   'Number of products per category',
            build(ctx){
                const labels = Object.keys(productsByCategory);
                const data   = Object.values(productsByCategory);
                const palette = ['#3b82f6','#06b6d4','#10b981','#f59e0b','#f97316','#ef4444','#8b5cf6','#ec4899'];
                return new Chart(ctx, {
                    type: 'bar',
                    data:{
                        labels,
                        datasets:[{
                            label: 'Products',
                            data,
                            backgroundColor: labels.map((_,i)=>palette[i%palette.length]),
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    plugins: [whiteBgPlugin],
                    options:{
                        indexAxis: 'y',
                        responsive: true,
                        plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.parsed.x} products`}}},
                        scales:{
                            x:{beginAtZero:true,grid:{color:'#f1f5f9'},ticks:{stepSize:1}},
                            y:{grid:{display:false}}
                        }
                    }
                });
            }
        },
        users: {
            title: '👥 New Users — Last 7 Days',
            sub:   'New user accounts registered per day',
            build(ctx){
                const labels = last7Labels();
                const data   = mapToLast7(usersByDay, 'cnt');
                return new Chart(ctx, {
                    type: 'bar',
                    data:{
                        labels,
                        datasets:[{
                            label: 'New Users',
                            data,
                            backgroundColor: 'rgba(124,58,237,.75)',
                            borderColor: '#7c3aed',
                            borderRadius: 6,
                            borderWidth: 1,
                        }]
                    },
                    plugins: [whiteBgPlugin],
                    options:{
                        responsive: true,
                        plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.parsed.y} users`}}},
                        scales:{
                            x:{grid:{display:false}},
                            y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#f1f5f9'}}
                        }
                    }
                });
            }
        },
        reviews: {
            title: '⭐ Reviews per Day (Last 7 Days)',
            sub:   'New reviews per day with average rating',
            build(ctx){
                const labels  = last7Labels();
                const counts  = mapToLast7(reviewsByDay, 'cnt');
                return new Chart(ctx, {
                    type: 'bar',
                    data:{
                        labels,
                        datasets:[{
                            label: 'Reviews',
                            data: counts,
                            backgroundColor: 'rgba(202,138,4,.7)',
                            borderColor: '#ca8a04',
                            borderRadius: 6,
                            borderWidth: 1,
                        }]
                    },
                    plugins: [whiteBgPlugin],
                    options:{
                        responsive: true,
                        plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.parsed.y} reviews`}}},
                        scales:{
                            x:{grid:{display:false}},
                            y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#f1f5f9'}}
                        }
                    }
                });
            }
        }
    };

    document.querySelectorAll('.stat-clickable').forEach(card=>{
        card.addEventListener('click',()=>{
            const key = card.dataset.chart;
            const def = chartDefs[key];
            if(!def) return;

            currentChartKey = key;
            titleEl.textContent = def.title;
            subEl.textContent   = def.sub;

            if(activeChart){ activeChart.destroy(); activeChart=null; }

            const wrap = document.querySelector('#chartModal .modal-body');
            wrap.innerHTML = '<canvas id="chartModalCanvas" style="max-height:300px;width:100%"></canvas><p style="margin-top:10px;font-size:11px;color:#94a3b8"><i class="fas fa-mouse-pointer" style="margin-right:4px"></i>Right-click the chart &rarr; <b>Copy image</b> or use <b>Save PNG</b> button above</p>';
            const ctx = document.getElementById('chartModalCanvas').getContext('2d');
            activeChart = def.build(ctx);
            modal.show();
        });
    });

    document.getElementById('chartModal').addEventListener('hidden.bs.modal',()=>{
        if(activeChart){ activeChart.destroy(); activeChart=null; }
    });

    // ── Inline Reviews-by-day chart (always visible on dashboard) ──
    (function(){
        const el = document.getElementById('reviewsByDayChart');
        if(!el) return;
        const labels = last7Labels();
        const counts = mapToLast7(reviewsByDay, 'cnt');
        new Chart(el.getContext('2d'), {
            type: 'line',
            data:{
                labels,
                datasets:[{
                    label: 'Rating',
                    data: counts,
                    borderColor: '#ca8a04',
                    backgroundColor: 'rgba(202,138,4,.1)',
                    pointBackgroundColor: '#ca8a04',
                    pointRadius: 5,
                    tension: 0.35,
                    fill: true,
                }]
            },
            options:{
                responsive:true,
                plugins:{
                    legend:{display:false},
                    tooltip:{callbacks:{label:c=>` ${c.parsed.y} reviews`}}
                },
                scales:{
                    x:{grid:{display:false}},
                    y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#f1f5f9'}}
                }
            }
        });
    })();
})();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>