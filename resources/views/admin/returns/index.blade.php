@extends('layouts.admin')

@section('title', 'Return Request')

@section('body-content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="pg-hdr">
    <div>
        <h2>🔄 Return Requests / Refunds</h2>
        <p>Review and process return requests from customers</p>
    </div>
</div>

{{-- Stats (clickable) --}}
@php
$rtCards = [
    ['id'=>'overview', 'val'=>$stats['pending'],   'lbl'=>'Pending',  'icon'=>'fa-clock',       'ibg'=>'#fef3c7','ic'=>'#d97706','accentBg'=>'#fffbeb','accentBorder'=>'#fcd34d'],
    ['id'=>'type',     'val'=>$stats['approved'],  'lbl'=>'Approved',     'icon'=>'fa-check',       'ibg'=>'#dbeafe','ic'=>'#1d4ed8','accentBg'=>'#eff6ff','accentBorder'=>'#93c5fd'],
    ['id'=>'trend',    'val'=>$stats['rejected'],  'lbl'=>'Reject',       'icon'=>'fa-times',       'ibg'=>'#fee2e2','ic'=>'#dc2626','accentBg'=>'#fef2f2','accentBorder'=>'#fca5a5'],
    ['id'=>'done',     'val'=>$stats['completed'], 'lbl'=>'Completed',    'icon'=>'fa-check-double','ibg'=>'#dcfce7','ic'=>'#16a34a','accentBg'=>'#f0fdf4','accentBorder'=>'#86efac'],
];
@endphp
<div class="row g-3 mb-4">
    @foreach($rtCards as $c)
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
    @if($returns->count())
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Reason</th>
                    <th>Type</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($returns as $r)
                <tr>
                    <td style="color:var(--muted)">{{ $r->id }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $r->order_id) }}" style="color:var(--primary);font-weight:600">
                            #{{ $r->order?->order_number ?? $r->order_id }}
                        </a>
                    </td>
                    <td>{{ $r->user?->name ?? 'N/A' }}<br><span style="font-size:11px;color:var(--muted)">{{ $r->user?->email }}</span></td>
                    <td style="max-width:220px">
                        <div style="font-size:12px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $r->reason }}">{{ $r->reason }}</div>
                        @if($r->admin_note)
                        <div style="font-size:11px;color:var(--muted);margin-top:2px"><i class="fas fa-comment-alt"></i> {{ $r->admin_note }}</div>
                        @endif
                    </td>
                    <td><span class="bs {{ $r->return_type === 'refund' ? 'bs-medium' : 'bs-low' }}">{{ $r->return_type === 'refund' ? 'Refund' : 'Exchange' }}</span></td>
                    <td style="color:var(--muted);font-size:12px">{{ $r->created_at?->format('d/m/Y H:i') }}</td>
                    <td>
                        @php
                        $stMap = ['pending'=>'bs-medium','approved'=>'bs-low','rejected'=>'bs-high','completed'=>'bs-active'];
                        $stLabel = ['pending'=>'Pending','approved'=>'Approved','rejected'=>'Reject','completed'=>'Completed'];
                        @endphp
                        <span class="bs {{ $stMap[$r->status] ?? '' }}">{{ $stLabel[$r->status] ?? $r->status }}</span>
                    </td>
                    <td>
                        <button class="btn-icon btn-icon-primary" title="Update"
                            onclick="openUpdate({{ $r->id }}, '{{ $r->status }}', '{{ addslashes($r->admin_note ?? '') }}')">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-3 pb-3">{{ $returns->links() }}</div>
    @else
    <div class="empty-state"><i class="fas fa-box-open"></i><p>No return requests yet</p></div>
    @endif
</div>

{{-- Update Modal --}}
<div class="modal fade" id="updateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e293b,#0f172a);border:none">
                <h5 class="modal-title" style="color:#fff;font-size:15px">🔄 Update Return Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Status</label>
                        <select name="status" class="form-select form-select-sm" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Note for customer</label>
                        <textarea name="admin_note" class="form-control form-control-sm" rows="3" placeholder="Reason or instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const returnsByStatus = @json($returnsByStatus);
const returnsByType   = @json($returnsByType);
const returnsByDay    = @json($returnsByDay);

const chartModal   = new bootstrap.Modal(document.getElementById('chartModal'));
const chartTitleEl = document.getElementById('chartModalTitle');
const chartSubEl   = document.getElementById('chartModalSub');
let   activeChart  = null;

function last7Labels(){
    const lbs=[];
    for(let i=6;i>=0;i--){const d=new Date();d.setDate(d.getDate()-i);lbs.push(d.getDate()+'/'+(d.getMonth()+1));}
    return lbs;
}
function last7Key(i){const d=new Date();d.setDate(d.getDate()-(6-i));return d.toISOString().slice(0,10);}

const chartDefs = {
    overview: {
        title: '🔄 Return Request Distribution',
        sub:   'All requests by current status',
        build(ctx){
            return new Chart(ctx,{type:'doughnut',data:{
                labels:Object.keys(returnsByStatus),
                datasets:[{data:Object.values(returnsByStatus),backgroundColor:['#f59e0b','#3b82f6','#ef4444','#10b981'],borderWidth:3,borderColor:'#fff',hoverOffset:8}]
            },options:{cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:16}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed} requests`}}}}});
        }
    },
    type: {
        title: '📌 Request type',
        sub:   'Refund and exchange request breakdowns',
        build(ctx){
            return new Chart(ctx,{type:'doughnut',data:{
                labels:Object.keys(returnsByType),
                datasets:[{data:Object.values(returnsByType),backgroundColor:['#3b82f6','#10b981'],borderWidth:3,borderColor:'#fff',hoverOffset:8}]
            },options:{cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:16}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed} requests`}}}}});
        }
    },
    trend: {
        title: '📈 Return Requests — Last 7 Days',
        sub:   'Requests submitted per day in the past week',
        build(ctx){
            const labels=last7Labels();
            const data=labels.map((_,i)=>{const k=last7Key(i);const r=returnsByDay.find(x=>x.day===k);return r?r.cnt:0;});
            return new Chart(ctx,{type:'bar',data:{
                labels,
                datasets:[{label:'Requests',data,backgroundColor:'#ef4444',borderRadius:6}]
            },options:{responsive:true,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.parsed.y} requests`}}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#f1f5f9'}}}}});
        }
    },
    done: {
        title: '✅ Request Processing Overview',
        sub:   'All requests by status',
        build(ctx){
            return new Chart(ctx,{type:'bar',data:{
                labels:Object.keys(returnsByStatus),
                datasets:[{label:'Requests',data:Object.values(returnsByStatus),backgroundColor:['#f59e0b','#3b82f6','#ef4444','#10b981'],borderRadius:6}]
            },options:{responsive:true,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.parsed.y} requests`}}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#f1f5f9'}}}}});
        }
    },
};

document.querySelectorAll('.stat-clickable').forEach(el=>{
    el.addEventListener('click',()=>{
        const key=el.dataset.chart;
        if(!chartDefs[key]) return;
        chartTitleEl.textContent=chartDefs[key].title;
        chartSubEl.textContent=chartDefs[key].sub;
        if(activeChart){activeChart.destroy();activeChart=null;}
        chartModal.show();
        document.getElementById('chartModal').addEventListener('shown.bs.modal',()=>{
            if(!activeChart) activeChart=chartDefs[key].build(document.getElementById('chartModalCanvas').getContext('2d'));
        },{once:true});
    });
});
document.getElementById('chartModal').addEventListener('hidden.bs.modal',()=>{
    if(activeChart){activeChart.destroy();activeChart=null;}
    const old=document.getElementById('chartModalCanvas');
    const fresh=document.createElement('canvas');
    fresh.id='chartModalCanvas';fresh.style.maxHeight='280px';fresh.style.width='100%';
    old.parentNode.replaceChild(fresh,old);
});

// ── Update Return Modal ───────────────────────────────────────────────────
const updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
function openUpdate(id, status, note) {
    const form = document.getElementById('updateForm');
    form.action = `/admin/returns/${id}`;
    form.querySelector('[name=status]').value     = status;
    form.querySelector('[name=admin_note]').value = note;
    updateModal.show();
}
</script>
@endsection
