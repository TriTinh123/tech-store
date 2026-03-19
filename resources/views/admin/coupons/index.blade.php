@extends('layouts.admin')

@section('title', 'Coupon code')

@section('body-content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="pg-hdr">
    <div>
        <h2>🎟 Coupon code</h2>
        <p>Create and manage promotional codes</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="fas fa-plus me-1"></i> Create new code
    </button>
</div>

{{-- Stats (clickable) --}}
@php
$active  = $coupons->where('is_active', true)->count();
$expired = $coupons->filter(fn($c) => $c->expires_at && $c->expires_at->isPast())->count();
$cpCards = [
    ['id'=>'type',   'val'=>$coupons->count(),         'lbl'=>'Total codes',          'icon'=>'fa-tags',        'ibg'=>'#dcfce7','ic'=>'#16a34a','accentBg'=>'#f0fdf4','accentBorder'=>'#86efac'],
    ['id'=>'status', 'val'=>$active,                   'lbl'=>'Active',   'icon'=>'fa-check-circle','ibg'=>'#dbeafe','ic'=>'#1d4ed8','accentBg'=>'#eff6ff','accentBorder'=>'#93c5fd'],
    ['id'=>'top',    'val'=>$expired,                  'lbl'=>'Expired',       'icon'=>'fa-clock',       'ibg'=>'#fef3c7','ic'=>'#d97706','accentBg'=>'#fffbeb','accentBorder'=>'#fcd34d'],
    ['id'=>'usage',  'val'=>$coupons->sum('used_count'),'lbl'=>'Total uses','icon'=>'fa-receipt',     'ibg'=>'#ede9fe','ic'=>'#7c3aed','accentBg'=>'#f5f3ff','accentBorder'=>'#c4b5fd'],
];
@endphp
<div class="row g-3 mb-4">
    @foreach($cpCards as $c)
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
            <div class="modal-body" style="padding:24px;background:#fff;min-height:300px;display:flex;align-items:center;justify-content:center">
                <canvas id="chartModalCanvas" style="max-height:280px;width:100%"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    @if($coupons->count())
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Min. Order</th>
                    <th>Used / Limit</th>
                    <th>Expires</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($coupons as $c)
                <tr>
                    <td><strong style="font-family:monospace;font-size:14px;letter-spacing:.5px">{{ $c->code }}</strong></td>
                    <td><span class="bs {{ $c->type==='percentage' ? 'bs-medium' : 'bs-low' }}">{{ $c->type === 'percentage' ? 'Percentage' : 'Fixed' }}</span></td>
                    <td>
                        @if($c->type === 'percent')
                            <strong>{{ $c->value }}%</strong>
                            @if($c->max_discount) <span style="color:var(--muted);font-size:11px">(max ${{ number_format($c->max_discount,2) }})</span>@endif
                        @else
                            <strong>${{ number_format($c->value,2) }}</strong>
                        @endif
                    </td>
                    <td style="color:var(--muted)">{{ $c->min_order_amount > 0 ? '$'.number_format($c->min_order_amount,2) : '—' }}</td>
                    <td>{{ $c->used_count }}{{ $c->usage_limit ? '/'.$c->usage_limit : '' }}</td>
                    <td style="color:var(--muted);font-size:12px">
                        @if($c->expires_at)
                            @if($c->expires_at->isPast())
                                <span style="color:#ef4444">{{ $c->expires_at->format('d/m/Y') }} (Expired)</span>
                            @else
                                {{ $c->expires_at->format('d/m/Y') }}
                            @endif
                        @else
                            No limit
                        @endif
                    </td>
                    <td>
                        <span class="bs {{ $c->is_active && (!$c->expires_at || !$c->expires_at->isPast()) ? 'bs-active' : 'bs-blocked' }}">
                            {{ $c->is_active && (!$c->expires_at || !$c->expires_at->isPast()) ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn-icon btn-icon-primary" title="Edit" onclick="openEdit({{ $c->id }}, '{{ $c->code }}', '{{ $c->type }}', {{ $c->value }}, {{ $c->min_order_amount }}, {{ $c->max_discount ?? 'null' }}, {{ $c->usage_limit ?? 'null' }}, '{{ $c->expires_at?->format("Y-m-d") ?? "" }}', {{ $c->is_active ? 'true' : 'false' }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.coupons.delete', $c->id) }}" method="POST" onsubmit="return confirm('Delete coupon {{ $c->code }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state"><i class="fas fa-tags"></i><p>No coupon codes yet</p></div>
    @endif
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e293b,#0f172a);border:none">
                <h5 class="modal-title" style="color:#fff;font-size:15px">🎟 Create New Coupon</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('admin.coupons._form')
                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Create code</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e293b,#0f172a);border:none">
                <h5 class="modal-title" style="color:#fff;font-size:15px">✏️ Edit Coupon</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body" id="editBody">
                    @include('admin.coupons._form')
                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// ── Chart data from server ────────────────────────────────────────────────
const couponByType   = @json($couponByType);
const couponByStatus = @json($couponByStatus);
const usagePerCoupon = @json($usagePerCoupon);

const chartModal    = new bootstrap.Modal(document.getElementById('chartModal'));
const chartTitleEl  = document.getElementById('chartModalTitle');
const chartSubEl    = document.getElementById('chartModalSub');
let   activeChart   = null;

const palette = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#ec4899'];

const chartDefs = {
    type: {
        title: '🎟 Coupon Type Distribution',
        sub:   'Coupons by type: Percentage and Fixed',
        build(ctx){
            return new Chart(ctx,{type:'doughnut',data:{
                labels: Object.keys(couponByType),
                datasets:[{data:Object.values(couponByType),backgroundColor:['#3b82f6','#10b981'],borderWidth:3,borderColor:'#fff',hoverOffset:8}]
            },options:{cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:16}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed} coupons`}}}}});
        }
    },
    status: {
        title: '✅ Coupon Status',
        sub:   'Active vs. inactive coupons',
        build(ctx){
            return new Chart(ctx,{type:'doughnut',data:{
                labels: Object.keys(couponByStatus),
                datasets:[{data:Object.values(couponByStatus),backgroundColor:['#10b981','#94a3b8'],borderWidth:3,borderColor:'#fff',hoverOffset:8}]
            },options:{cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:16}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed} coupons`}}}}});
        }
    },
    top: {
        title: '🏆 Top Most Used Coupons',
        sub:   'Ranked by number of times applied',
        build(ctx){
            const labels = usagePerCoupon.map(r=>r.code);
            const data   = usagePerCoupon.map(r=>r.used);
            return new Chart(ctx,{type:'bar',data:{
                labels,
                datasets:[{label:'Uses',data,backgroundColor:labels.map((_,i)=>palette[i%palette.length]),borderRadius:6,borderSkipped:false}]
            },options:{indexAxis:'y',responsive:true,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.parsed.x} uses`}}},scales:{x:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#f1f5f9'}},y:{grid:{display:false}}}}});
        }
    },
    usage: {
        title: '📊 Uses per Coupon',
        sub:   'Total uses per coupon code',
        build(ctx){
            const labels = usagePerCoupon.map(r=>r.code);
            const data   = usagePerCoupon.map(r=>r.used);
            return new Chart(ctx,{type:'bar',data:{
                labels,
                datasets:[{label:'Uses',data,backgroundColor:'#3b82f6',borderRadius:6}]
            },options:{responsive:true,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.parsed.y} uses`}}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#f1f5f9'}}}}});
        }
    },
};

document.querySelectorAll('.stat-clickable').forEach(el=>{
    el.addEventListener('click',()=>{
        const key = el.dataset.chart;
        if(!chartDefs[key]) return;
        chartTitleEl.textContent = chartDefs[key].title;
        chartSubEl.textContent   = chartDefs[key].sub;
        if(activeChart){ activeChart.destroy(); activeChart=null; }
        // Rebuild canvas fresh, then build chart, then show — same pattern as dashboard
        const wrap = document.querySelector('#chartModal .modal-body');
        wrap.innerHTML = '<canvas id="chartModalCanvas" style="max-height:280px;width:100%"></canvas>';
        const ctx = document.getElementById('chartModalCanvas').getContext('2d');
        activeChart = chartDefs[key].build(ctx);
        chartModal.show();
    });
});
document.getElementById('chartModal').addEventListener('hidden.bs.modal',()=>{
    if(activeChart){ activeChart.destroy(); activeChart=null; }
});

// ── Coupon CRUD modals ────────────────────────────────────────────────────
// Reset form whenever create modal opens (works even without JS-init)
document.getElementById('createModal').addEventListener('show.bs.modal', function(){
    this.querySelector('form').reset();
});

function openEdit(id, code, type, value, min, max, limit, exp, active) {
    const form = document.getElementById('editForm');
    form.action = `/admin/coupons/${id}`;
    const b = document.getElementById('editBody');
    b.querySelector('[name=code]').value     = code;
    b.querySelector('[name=type]').value     = type;
    b.querySelector('[name=value]').value    = value;
    b.querySelector('[name=min_order_amount]').value = min || '';
    b.querySelector('[name=max_discount]').value     = max || '';
    b.querySelector('[name=usage_limit]').value      = limit || '';
    b.querySelector('[name=expires_at]').value       = exp || '';
    b.querySelector('[name=is_active]').checked      = active;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>
@endsection
