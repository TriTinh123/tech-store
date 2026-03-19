@extends('layouts.admin')
@section('title', 'Rating products')

@section('body-content')
<style>
    .star-bar { display:flex; align-items:center; gap:8px; margin-bottom:6px; }
    .star-bar-fill { height:6px; border-radius:3px; background:#f59e0b; }
    .star-bar-bg { flex:1; height:6px; background:#e8edf2; border-radius:3px; overflow:hidden; }
    .rv-stars { color:#f59e0b; font-size:12px; letter-spacing:1px; }
    .rv-stars .empty { color:#d1d5db; }
    .rv-avatar { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0; }
    .filter-bar { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px; align-items:center; }
    .filter-bar select, .filter-bar input { padding:7px 12px; border:1.5px solid #e8edf2; border-radius:8px; font-size:13px; background:#fff; }
    .filter-bar select:focus, .filter-bar input:focus { outline:none; border-color:#3b82f6; }
</style>

{{-- Flash --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:10px;font-size:13px">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Stats Row --}}
@php
$rvCards = [
    ['id'=>'distribution', 'val'=>$stats['total'],                   'lbl'=>'Total Reviews',  'icon'=>'fa-star',         'ibg'=>'#fef3c7','ic'=>'#d97706','accentBg'=>'#fffbeb','accentBorder'=>'#fcd34d'],
    ['id'=>'avgRating',    'val'=>($stats['avg']?:'—').'★',         'lbl'=>'Avg. Rating',    'icon'=>'fa-star-half-alt','ibg'=>'#dcfce7','ic'=>'#16a34a','accentBg'=>'#f0fdf4','accentBorder'=>'#86efac'],
    ['id'=>'fiveStars',    'val'=>$stats['five'],                    'lbl'=>'5 Stars',        'icon'=>'fa-thumbs-up',   'ibg'=>'#dbeafe','ic'=>'#1d4ed8','accentBg'=>'#eff6ff','accentBorder'=>'#93c5fd'],
    ['id'=>'lowRatings',   'val'=>($stats['one']+$stats['two']),    'lbl'=>'1–2 Stars (low)','icon'=>'fa-thumbs-down', 'ibg'=>'#fee2e2','ic'=>'#dc2626','accentBg'=>'#fef2f2','accentBorder'=>'#fca5a5'],
];
@endphp
<div class="row g-3 mb-4">
    @foreach($rvCards as $c)
    <div class="col-6 col-md-3">
        <div class="stat-card stat-clickable" data-chart="{{ $c['id'] }}"
             style="cursor:pointer;transition:all .2s;border:1.5px solid transparent"
             onmouseenter="this.style.borderColor='{{ $c['accentBorder'] }}';this.style.background='{{ $c['accentBg'] }}';this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,.09)'"
             onmouseleave="this.style.borderColor='transparent';this.style.background='var(--card)';this.style.transform='';this.style.boxShadow='var(--shadow)'">
            <div class="stat-icon" style="background:{{ $c['ibg'] }}"><i class="fas {{ $c['icon'] }}" style="color:{{ $c['ic'] }}"></i></div>
            <div style="flex:1"><div class="stat-val">{{ $c['val'] }}</div><div class="stat-lbl">{{ $c['lbl'] }}</div></div>
            <div style="font-size:11px;color:#94a3b8;align-self:flex-end;padding-bottom:2px"><i class="fas fa-chart-bar"></i></div>
        </div>
    </div>
    @endforeach
</div>

{{-- Chart Modal --}}
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

{{-- Rating Distribution --}}
<div class="card mb-4">
    <div class="card-header"><h6 class="mb-0">Review distribution</h6></div>
    <div class="card-body">
        <div class="row g-2">
            @foreach([5,4,3,2,1] as $star)
            @php $cnt = $stats[['','one','two','three','four','five'][$star]]; $pct = $stats['total'] > 0 ? round($cnt/$stats['total']*100) : 0; @endphp
            <div class="col-12 col-md-6">
                <div class="star-bar">
                    <span style="font-size:12px;font-weight:600;width:18px;text-align:right">{{ $star }}</span>
                    <i class="fas fa-star" style="color:#f59e0b;font-size:11px"></i>
                    <div class="star-bar-bg"><div class="star-bar-fill" style="width:{{ $pct }}%"></div></div>
                    <span style="font-size:12px;color:#64748b;width:50px">{{ $cnt }} ({{ $pct }}%)</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.reviews') }}" class="filter-bar">
    <input type="text" name="search" placeholder="Search name / content..." value="{{ request('search') }}" style="min-width:220px">
    <select name="rating">
        <option value="">All star ratings</option>
        @foreach([5,4,3,2,1] as $s)
        <option value="{{ $s }}" {{ request('rating') == $s ? 'selected' : '' }}>{{ $s }} stars</option>
        @endforeach
    </select>
    <select name="product_id">
        <option value="">All Products</option>
        @foreach($products as $p)
        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-sm btn-primary" style="border-radius:8px;font-size:13px"><i class="fas fa-search me-1"></i>Filter</button>
    @if(request('search') || request('rating') || request('product_id'))
    <a href="{{ route('admin.reviews') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:13px">Clear filters</a>
    @endif
</form>

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Reviews <span style="color:#94a3b8;font-weight:400">({{ $reviews->total() }} results)</span></h6>
    </div>
    @if($reviews->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover" style="font-size:13px">
            <thead>
                <tr>
                    <th>Users</th>
                    <th>Products</th>
                    <th>Rating</th>
                    <th>Content</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="rv-avatar">{{ strtoupper(substr($review->user_name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <div style="font-weight:600;color:#1a1f2e">{{ $review->user_name }}</div>
                                <div style="font-size:11px;color:#94a3b8">{{ $review->user_email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($review->product)
                        <a href="{{ route('product.show', $review->product->id) }}" target="_blank"
                           style="color:#0984e3;text-decoration:none;font-weight:500;font-size:12px">
                            {{ Str::limit($review->product->name, 40) }}
                        </a>
                        @else
                        <span style="color:#94a3b8">Product deleted</span>
                        @endif
                    </td>
                    <td>
                        <div class="rv-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i > $review->rating ? ' empty' : '' }}"></i>
                            @endfor
                        </div>
                        <div style="font-size:11px;color:#64748b;margin-top:2px">{{ $review->rating }}/5</div>
                    </td>
                    <td style="max-width:260px">
                        @if($review->comment)
                        <div style="color:#374151;line-height:1.5">{{ Str::limit($review->comment, 100) }}</div>
                        @else
                        <span style="color:#d1d5db;font-style:italic">No content</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;color:#64748b">
                        {{ $review->created_at->format('d/m/Y') }}<br>
                        <span style="font-size:11px">{{ $review->created_at->format('H:i') }}</span>
                    </td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('admin.reviews.delete', $review) }}"
                              onsubmit="return confirm('Delete this review?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:6px;font-size:11px">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center" style="font-size:12px;color:#64748b">
        <span>Show {{ $reviews->firstItem() }}–{{ $reviews->lastItem() }} / {{ $reviews->total() }}</span>
        {{ $reviews->links() }}
    </div>
    @else
    <div class="card-body text-center py-5" style="color:#94a3b8">
        <i class="fas fa-star fa-3x mb-3" style="opacity:.25"></i>
        <p class="mb-0">No reviews yet</p>
    </div>
    @endif
</div>
@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function(){
    const whiteBgPlugin = {
        id:'whiteBg',
        beforeDraw(chart){ const {ctx}=chart; ctx.save(); ctx.globalCompositeOperation='destination-over'; ctx.fillStyle='#fff'; ctx.fillRect(0,0,chart.width,chart.height); ctx.restore(); }
    };
    const ratingData = {
        five:  {{ $stats['five'] }},
        four:  {{ $stats['four'] }},
        three: {{ $stats['three'] }},
        two:   {{ $stats['two'] }},
        one:   {{ $stats['one'] }},
    };
    const modal   = new bootstrap.Modal(document.getElementById('chartModal'));
    const titleEl = document.getElementById('chartModalTitle');
    const subEl   = document.getElementById('chartModalSub');
    let activeChart = null, currentKey = null;
    const palette = ['#f59e0b','#3b82f6','#10b981','#f97316','#ef4444'];
    const chartDefs = {
        distribution:{
            title:'⭐ Rating Distribution', sub:'Number of reviews per star rating',
            build(ctx){
                return new Chart(ctx,{type:'bar',data:{labels:['5 Stars','4 Stars','3 Stars','2 Stars','1 Star'],datasets:[{label:'Reviews',data:[ratingData.five,ratingData.four,ratingData.three,ratingData.two,ratingData.one],backgroundColor:palette,borderRadius:6,borderSkipped:false}]},plugins:[whiteBgPlugin],options:{responsive:true,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.parsed.y} reviews`}}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#f1f5f9'}}}}});
            }
        },
        avgRating:{
            title:'📊 Rating Breakdown', sub:'Distribution of all star ratings',
            build(ctx){
                return new Chart(ctx,{type:'doughnut',data:{labels:['5 Stars','4 Stars','3 Stars','2 Stars','1 Star'],datasets:[{data:[ratingData.five,ratingData.four,ratingData.three,ratingData.two,ratingData.one],backgroundColor:['#f59e0b','#3b82f6','#10b981','#f97316','#ef4444'],borderWidth:3,borderColor:'#fff',hoverOffset:8}]},plugins:[whiteBgPlugin],options:{cutout:'55%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:12}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed}`}}}}});
            }
        },
        fiveStars:{
            title:'🌟 5-Star Reviews', sub:'Top-rated vs everything else',
            build(ctx){
                const rest = ratingData.four+ratingData.three+ratingData.two+ratingData.one;
                return new Chart(ctx,{type:'doughnut',data:{labels:['5 Stars','Other ratings'],datasets:[{data:[ratingData.five,rest],backgroundColor:['#f59e0b','#e2e8f0'],borderWidth:3,borderColor:'#fff',hoverOffset:8}]},plugins:[whiteBgPlugin],options:{cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:16}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed}`}}}}});
            }
        },
        lowRatings:{
            title:'⚠️ Low Ratings (1–2 Stars)', sub:'Negative reviews vs positive',
            build(ctx){
                const high = ratingData.five+ratingData.four+ratingData.three;
                const low  = ratingData.two+ratingData.one;
                return new Chart(ctx,{type:'doughnut',data:{labels:['1–2 Stars','3–5 Stars'],datasets:[{data:[low,high],backgroundColor:['#ef4444','#10b981'],borderWidth:3,borderColor:'#fff',hoverOffset:8}]},plugins:[whiteBgPlugin],options:{cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:16}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed}`}}}}});
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
        const a=document.createElement('a');a.href=o.toDataURL('image/png');a.download=(currentKey||'chart')+'-reviews.png';a.click();
    });
})();
</script>
@endsection
