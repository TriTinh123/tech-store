

<?php $__env->startSection('title', 'Security Log'); ?>

<?php $__env->startSection('body-content'); ?>

<div class="pg-hdr">
    <div>
        <h2>🛡 AI Login Audit</h2>
        <p>Login history with Isolation Forest risk analysis</p>
    </div>
</div>


<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['id'=>'total',    'val'=>$stats['total']??0,        'lbl'=>'Total logins', 'icon'=>'fa-clipboard-list', 'ibg'=>'#dbeafe','ic'=>'#1d4ed8','accentBg'=>'#eff6ff','accentBorder'=>'#93c5fd'],
        ['id'=>'failed',   'val'=>$stats['failed']??0,       'lbl'=>'Failed Sign-Ins',  'icon'=>'fa-times-circle',   'ibg'=>'#fee2e2','ic'=>'#dc2626','accentBg'=>'#fef2f2','accentBorder'=>'#fca5a5'],
        ['id'=>'anomaly',  'val'=>$stats['anomaly']??0,      'lbl'=>'Anomaly Detected', 'icon'=>'fa-exclamation-triangle','ibg'=>'#fef3c7','ic'=>'#d97706','accentBg'=>'#fffbeb','accentBorder'=>'#fcd34d'],
        ['id'=>'tfa',      'val'=>$stats['required_3fa']??0, 'lbl'=>'3FA Required',         'icon'=>'fa-shield-alt',     'ibg'=>'#ede9fe','ic'=>'#7c3aed','accentBg'=>'#f5f3ff','accentBorder'=>'#c4b5fd'],
    ];
    ?>
    <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                <div>
                    <h5 class="modal-title" id="chartModalTitle" style="color:#fff;font-size:15px;font-weight:600;margin:0"></h5>
                    <p id="chartModalSub" style="color:#94a3b8;font-size:12px;margin:3px 0 0"></p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;background:#fff;min-height:320px;display:flex;align-items:center;justify-content:center">
                <canvas id="chartModalCanvas" style="max-height:300px;width:100%"></canvas>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6>Login Details</h6>
        <span style="font-size:12px;color:var(--muted)"><?php echo e($attempts->total()); ?> records</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" style="min-width:900px">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Users</th>
                    <th>IP</th>
                    <th style="text-align:center">Risk Score</th>
                    <th style="text-align:center">Level</th>
                    <th style="text-align:center">Anomalous</th>
                    <th style="text-align:center">3FA</th>
                    <th style="text-align:center">Result</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $attempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $lvl = $a->risk_level ?? 'low';
                    $barColor = match($lvl) {
                        'critical' => '#ef4444',
                        'high'     => '#f97316',
                        'medium'   => '#eab308',
                        default    => '#22c55e',
                    };
                ?>
                <tr>
                    <td style="white-space:nowrap;color:var(--muted)"><?php echo e($a->created_at->format('d/m H:i:s')); ?></td>
                    <td>
                        <?php if($a->user): ?>
                            <div style="font-weight:500"><?php echo e($a->user->name); ?></div>
                            <div style="font-size:11.5px;color:var(--muted)"><?php echo e($a->email); ?></div>
                        <?php else: ?>
                            <span style="color:var(--muted)"><?php echo e($a->email); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-family:monospace;font-size:12.5px;color:#2563eb"><?php echo e($a->ip_address); ?></div>
                        <?php if($a->geo_country_code): ?>
                        <?php
                            $flag = strtolower($a->geo_country_code ?? '');
                            $flag = implode('', array_map(fn($c) => mb_chr(ord($c) - ord('a') + 0x1F1E6), str_split($flag)));
                        ?>
                        <div style="font-size:11.5px;color:<?php echo e($a->geo_is_foreign_risk ? '#ef4444' : 'var(--muted)'); ?>">
                            <?php echo e($flag); ?> <?php echo e($a->geo_city ? $a->geo_city.', ' : ''); ?><?php echo e($a->geo_country); ?>

                            <?php if($a->geo_is_foreign_risk): ?> <span title="High-risk country">⚠</span> <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center">
                        <?php if($a->risk_numeric !== null): ?>
                        <div style="display:flex;flex-direction:column;align-items:center;gap:3px">
                            <span style="font-weight:700;font-size:15px;color:<?php echo e($barColor); ?>"><?php echo e($a->risk_numeric); ?></span>
                            <div style="width:56px;height:5px;background:#e2e8f0;border-radius:3px;overflow:hidden">
                                <div style="width:<?php echo e($a->risk_numeric); ?>%;height:100%;background:<?php echo e($barColor); ?>;border-radius:3px"></div>
                            </div>
                        </div>
                        <?php else: ?> <span style="color:var(--muted)">—</span> <?php endif; ?>
                    </td>
                    <td style="text-align:center">
                        <?php if($a->risk_level): ?>
                            <span class="bs bs-<?php echo e($lvl); ?>"><?php echo e(strtoupper($lvl)); ?></span>
                        <?php else: ?> <span style="color:var(--muted)">—</span> <?php endif; ?>
                    </td>
                    <td style="text-align:center">
                        <?php if($a->is_anomaly): ?>
                            <span style="color:#ef4444;font-weight:600;font-size:13px">⚠ Yes</span>
                        <?php else: ?>
                            <span style="color:#10b981;font-size:13px">✓</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;font-size:13px">
                        <?php if($a->required_3fa): ?>
                            <span style="color:<?php echo e($a->passed_3fa ? '#10b981' : '#ef4444'); ?>;font-weight:600">
                                <?php echo e($a->passed_3fa ? '✓ Pass' : '✗ Fail'); ?>

                            </span>
                        <?php else: ?>
                            <span style="color:var(--muted)">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;font-weight:700">
                        <?php if($a->success): ?>
                            <span style="color:#10b981">✅</span>
                        <?php else: ?>
                            <span style="color:#ef4444">❌</span>
                        <?php endif; ?>
                    </td>
                    <td style="max-width:220px">
                        <?php
                            $explanations = is_array($a->explanation)
                                ? $a->explanation
                                : (is_string($a->explanation) && str_starts_with(trim($a->explanation), '[')
                                    ? (json_decode($a->explanation, true) ?? [$a->explanation])
                                    : ($a->explanation ? [$a->explanation] : []));
                        ?>
                        <?php if(count($explanations)): ?>
                            <ul style="list-style:none;padding:0;margin:0">
                                <?php $__currentLoopData = array_slice($explanations, 0, 2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li style="font-size:11.5px;color:var(--muted);line-height:1.5">• <?php echo e($reason); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($explanations) > 2): ?>
                                    <li style="font-size:11px;color:var(--muted);opacity:.7">+<?php echo e(count($explanations) - 2); ?> more…</li>
                                <?php endif; ?>
                            </ul>
                        <?php else: ?> <span style="color:var(--muted);font-size:12px">—</span> <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="fas fa-clipboard"></i>
                            <p>No login data yet</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($attempts->hasPages()): ?>
    <div class="card-footer">
        <?php echo e($attempts->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('extra-js'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function(){
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#64748b';

    const riskRaw  = <?php echo json_encode($riskDist, 15, 512) ?>;
    const daily    = <?php echo json_encode($daily, 15, 512) ?>;
    const hourData = <?php echo json_encode($hourData, 15, 512) ?>;
    const total    = <?php echo e($stats['total'] ?? 0); ?>;
    const failed   = <?php echo e($stats['failed'] ?? 0); ?>;
    const anomaly  = <?php echo e($stats['anomaly'] ?? 0); ?>;
    const tfa      = <?php echo e($stats['required_3fa'] ?? 0); ?>;

    const riskOrder  = ['low','medium','high','critical'];
    const riskLabels = {low:'LOW',medium:'MEDIUM',high:'HIGH',critical:'CRITICAL'};
    const riskColors = {low:'#10b981',medium:'#f59e0b',high:'#f97316',critical:'#ef4444'};

    const modal   = new bootstrap.Modal(document.getElementById('chartModal'));
    const titleEl = document.getElementById('chartModalTitle');
    const subEl   = document.getElementById('chartModalSub');
    let activeChart = null;

    const chartDefs = {
        total: {
            title: '📋 Total Logins by Hour',
            sub:   'Login time distribution throughout the day (0h–23h)',
            build(ctx){
                return new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Array.from({length:24},(_,i)=>i+'h'),
                        datasets:[{
                            label:'Logins',
                            data: hourData,
                            backgroundColor: 'rgba(59,130,246,.7)',
                            borderColor: '#3b82f6',
                            borderRadius: 5,
                            borderWidth: 1,
                        }]
                    },
                    options:{
                        responsive:true,
                        plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.parsed.y} logins`}}},
                        scales:{
                            x:{grid:{display:false},ticks:{font:{size:10},maxTicksLimit:12}},
                            y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#f1f5f9'}}
                        }
                    }
                });
            }
        },
        failed: {
            title: '❌ Success vs Failed (last 7 days)',
            sub:   'Track failed login trends over the week',
            build(ctx){
                const labels = daily.map(d=>{ const dt=new Date(d.day); return dt.getDate()+'/'+(dt.getMonth()+1); });
                return new Chart(ctx, {
                    type:'bar',
                    data:{
                        labels: labels.length ? labels : ['No data available'],
                        datasets:[
                            {label:'Success',data:daily.map(d=>d.ok),  backgroundColor:'#10b981',borderRadius:5,borderSkipped:false},
                            {label:'Failed',  data:daily.map(d=>d.fail),backgroundColor:'#ef4444',borderRadius:5,borderSkipped:false},
                        ]
                    },
                    options:{
                        responsive:true,
                        plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:16}}},
                        scales:{
                            x:{grid:{display:false}},
                            y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#f1f5f9'}}
                        }
                    }
                });
            }
        },
        anomaly: {
            title: '⚠️ Anomaly Detected (Isolation Forest)',
            sub:   'Normal vs. anomalous login session ratio',
            build(ctx){
                return new Chart(ctx, {
                    type:'doughnut',
                    data:{
                        labels:['Normal','Anomalous'],
                        datasets:[{
                            data:[total - anomaly, anomaly],
                            backgroundColor:['#10b981','#f97316'],
                            borderWidth:3,
                            borderColor:'#fff',
                            hoverOffset:8,
                        }]
                    },
                    options:{
                        cutout:'60%',
                        plugins:{
                            legend:{position:'bottom',labels:{boxWidth:12,padding:16}},
                            tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed} times (${Math.round(c.parsed/total*100)}%)`}}
                        }
                    }
                });
            }
        },
        tfa: {
            title: '🛡 3FA Verification Required',
            sub:   'Risk level distribution — LOW / MEDIUM / HIGH / CRITICAL',
            build(ctx){
                const rl = riskOrder.filter(k=>riskRaw[k]!=null);
                return new Chart(ctx, {
                    type:'doughnut',
                    data:{
                        labels: rl.map(k=>riskLabels[k]),
                        datasets:[{
                            data: rl.map(k=>riskRaw[k]),
                            backgroundColor: rl.map(k=>riskColors[k]),
                            borderWidth:3,
                            borderColor:'#fff',
                            hoverOffset:8,
                        }]
                    },
                    options:{
                        cutout:'60%',
                        plugins:{
                            legend:{position:'bottom',labels:{boxWidth:12,padding:16}},
                            tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed} times`}}
                        }
                    }
                });
            }
        }
    };

    document.querySelectorAll('.stat-clickable').forEach(card=>{
        card.addEventListener('click',()=>{
            const key  = card.dataset.chart;
            const def  = chartDefs[key];
            if(!def) return;

            titleEl.textContent = def.title;
            subEl.textContent   = def.sub;

            // destroy previous chart
            if(activeChart){ activeChart.destroy(); activeChart=null; }

            // replace canvas (Chart.js requires fresh canvas to redraw)
            const wrap = document.querySelector('#chartModal .modal-body');
            wrap.innerHTML = '<canvas id="chartModalCanvas" style="max-height:300px;width:100%"></canvas>';
            const ctx = document.getElementById('chartModalCanvas').getContext('2d');
            activeChart = def.build(ctx);
            modal.show();
        });
    });

    document.getElementById('chartModal').addEventListener('hidden.bs.modal',()=>{
        if(activeChart){ activeChart.destroy(); activeChart=null; }
    });
})();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\admin\security\index.blade.php ENDPATH**/ ?>