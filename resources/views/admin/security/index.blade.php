@extends('layouts.admin')

@section('title', 'Security Log')

@section('body-content')

<div class="pg-hdr">
    <div>
        <h2>🛡 AI Login Audit</h2>
        <p>Login history with Isolation Forest risk analysis</p>
    </div>
</div>

{{-- Stats (clickable) --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['id'=>'total',    'val'=>$stats['total']??0,        'lbl'=>'Total logins', 'icon'=>'fa-clipboard-list', 'ibg'=>'#dbeafe','ic'=>'#1d4ed8','accentBg'=>'#eff6ff','accentBorder'=>'#93c5fd'],
        ['id'=>'failed',   'val'=>$stats['failed']??0,       'lbl'=>'Failed Sign-Ins',  'icon'=>'fa-times-circle',   'ibg'=>'#fee2e2','ic'=>'#dc2626','accentBg'=>'#fef2f2','accentBorder'=>'#fca5a5'],
        ['id'=>'anomaly',  'val'=>$stats['anomaly']??0,      'lbl'=>'Anomaly Detected', 'icon'=>'fa-exclamation-triangle','ibg'=>'#fef3c7','ic'=>'#d97706','accentBg'=>'#fffbeb','accentBorder'=>'#fcd34d'],
        ['id'=>'tfa',      'val'=>$stats['required_3fa']??0, 'lbl'=>'3FA Required',         'icon'=>'fa-shield-alt',     'ibg'=>'#ede9fe','ic'=>'#7c3aed','accentBg'=>'#f5f3ff','accentBorder'=>'#c4b5fd'],
    ];
    @endphp
    @foreach($cards as $c)
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-clickable" data-chart="{{ $c['id'] }}"
             style="cursor:pointer;transition:all .2s;border:1.5px solid transparent"
             onmouseenter="this.style.borderColor='{{ $c['accentBorder'] }}';this.style.background='{{ $c['accentBg'] }}';this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,.09)'"
             onmouseleave="this.style.borderColor='transparent';this.style.background='var(--card)';this.style.transform='';this.style.boxShadow='var(--shadow)'">
            <div class="stat-icon" style="background:{{ $c['ibg'] }}">
                <i class="fas {{ $c['icon'] }}" style="color:{{ $c['ic'] }}"></i>
            </div>
            <div style="flex:1">
                <div class="stat-val">{{ $c['val'] }}</div>
                <div class="stat-lbl">{{ $c['lbl'] }}</div>
            </div>
            <div style="font-size:11px;color:#94a3b8;align-self:flex-end;padding-bottom:2px">
                <i class="fas fa-chart-bar"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Chart Modal --}}
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

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6>Login Details</h6>
        <span style="font-size:12px;color:var(--muted)">{{ $attempts->total() }} records</span>
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
                @forelse($attempts as $a)
                @php
                    $lvl = $a->risk_level ?? 'low';
                    $barColor = match($lvl) {
                        'critical' => '#ef4444',
                        'high'     => '#f97316',
                        'medium'   => '#eab308',
                        default    => '#22c55e',
                    };
                @endphp
                <tr>
                    <td style="white-space:nowrap;color:var(--muted)">{{ $a->created_at->format('d/m H:i:s') }}</td>
                    <td>
                        @if($a->user)
                            <div style="font-weight:500">{{ $a->user->name }}</div>
                            <div style="font-size:11.5px;color:var(--muted)">{{ $a->email }}</div>
                        @else
                            <span style="color:var(--muted)">{{ $a->email }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-family:monospace;font-size:12.5px;color:#2563eb">{{ $a->ip_address }}</div>
                        @if($a->geo_country_code)
                        @php
                            $flag = strtolower($a->geo_country_code ?? '');
                            $flag = implode('', array_map(fn($c) => mb_chr(ord($c) - ord('a') + 0x1F1E6), str_split($flag)));
                        @endphp
                        <div style="font-size:11.5px;color:{{ $a->geo_is_foreign_risk ? '#ef4444' : 'var(--muted)' }}">
                            {{ $flag }} {{ $a->geo_city ? $a->geo_city.', ' : '' }}{{ $a->geo_country }}
                            @if($a->geo_is_foreign_risk) <span title="High-risk country">⚠</span> @endif
                        </div>
                        @endif
                    </td>
                    <td style="text-align:center">
                        @if($a->risk_numeric !== null)
                        <div style="display:flex;flex-direction:column;align-items:center;gap:3px">
                            <span style="font-weight:700;font-size:15px;color:{{ $barColor }}">{{ $a->risk_numeric }}</span>
                            <div style="width:56px;height:5px;background:#e2e8f0;border-radius:3px;overflow:hidden">
                                <div style="width:{{ $a->risk_numeric }}%;height:100%;background:{{ $barColor }};border-radius:3px"></div>
                            </div>
                        </div>
                        @else <span style="color:var(--muted)">—</span> @endif
                    </td>
                    <td style="text-align:center">
                        @if($a->risk_level)
                            <span class="bs bs-{{ $lvl }}">{{ strtoupper($lvl) }}</span>
                        @else <span style="color:var(--muted)">—</span> @endif
                    </td>
                    <td style="text-align:center">
                        @if($a->is_anomaly)
                            <span style="color:#ef4444;font-weight:600;font-size:13px">⚠ Yes</span>
                        @else
                            <span style="color:#10b981;font-size:13px">✓</span>
                        @endif
                    </td>
                    <td style="text-align:center;font-size:13px">
                        @if($a->required_3fa)
                            <span style="color:{{ $a->passed_3fa ? '#10b981' : '#ef4444' }};font-weight:600">
                                {{ $a->passed_3fa ? '✓ Pass' : '✗ Fail' }}
                            </span>
                        @else
                            <span style="color:var(--muted)">N/A</span>
                        @endif
                    </td>
                    <td style="text-align:center;font-weight:700">
                        @if($a->success)
                            <span style="color:#10b981">✅</span>
                        @else
                            <span style="color:#ef4444">❌</span>
                        @endif
                    </td>
                    <td style="max-width:220px">
                        @php
                            $explanations = is_array($a->explanation)
                                ? $a->explanation
                                : (is_string($a->explanation) && str_starts_with(trim($a->explanation), '[')
                                    ? (json_decode($a->explanation, true) ?? [$a->explanation])
                                    : ($a->explanation ? [$a->explanation] : []));
                        @endphp
                        @if(count($explanations))
                            <ul style="list-style:none;padding:0;margin:0">
                                @foreach(array_slice($explanations, 0, 2) as $reason)
                                    <li style="font-size:11.5px;color:var(--muted);line-height:1.5">• {{ $reason }}</li>
                                @endforeach
                                @if(count($explanations) > 2)
                                    <li style="font-size:11px;color:var(--muted);opacity:.7">+{{ count($explanations) - 2 }} more…</li>
                                @endif
                            </ul>
                        @else <span style="color:var(--muted);font-size:12px">—</span> @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="fas fa-clipboard"></i>
                            <p>No login data yet</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($attempts->hasPages())
    <div class="card-footer">
        {{ $attempts->links() }}
    </div>
    @endif
</div>

@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function(){
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#64748b';

    const riskRaw  = @json($riskDist);
    const daily    = @json($daily);
    const hourData = @json($hourData);
    const total    = {{ $stats['total'] ?? 0 }};
    const failed   = {{ $stats['failed'] ?? 0 }};
    const anomaly  = {{ $stats['anomaly'] ?? 0 }};
    const tfa      = {{ $stats['required_3fa'] ?? 0 }};

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
@endsection