@extends('layouts.admin')

@section('title', 'Products')

@section('body-content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="pg-hdr">
    <div>
        <h2>Products</h2>
        <p>Product Management</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Add product
    </a>
</div>

{{-- Stat Cards --}}
@php
$prodCards = [
    ['id'=>'byCategory', 'val'=>$totalProducts,   'lbl'=>'Total Products',   'icon'=>'fa-box',         'ibg'=>'#dbeafe','ic'=>'#1d4ed8','accentBg'=>'#eff6ff','accentBorder'=>'#93c5fd'],
    ['id'=>'inStock',    'val'=>$inStock,          'lbl'=>'In Stock',         'icon'=>'fa-check-circle','ibg'=>'#dcfce7','ic'=>'#16a34a','accentBg'=>'#f0fdf4','accentBorder'=>'#86efac'],
    ['id'=>'outOfStock', 'val'=>$outOfStock,       'lbl'=>'Out of Stock',     'icon'=>'fa-times-circle','ibg'=>'#fee2e2','ic'=>'#dc2626','accentBg'=>'#fef2f2','accentBorder'=>'#fca5a5'],
    ['id'=>'featured',   'val'=>$featuredCount,    'lbl'=>'Featured',         'icon'=>'fa-star',        'ibg'=>'#fef3c7','ic'=>'#d97706','accentBg'=>'#fffbeb','accentBorder'=>'#fcd34d'],
];
@endphp
<div class="row g-3 mb-4">
    @foreach($prodCards as $c)
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

<div class="card">
    @if($products && count($products) > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product name</th>
                    <th>Categories</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Featured</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td style="color:var(--muted)">{{ $product->id }}</td>
                    <td>
                        <div style="font-weight:500">{{ $product->name }}</div>
                        @if($product->manufacturer)
                        <div style="font-size:11.5px;color:var(--muted)">{{ $product->manufacturer }}</div>
                        @endif
                    </td>
                    <td>{{ $product->categoryModel?->name ?? '—' }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>
                        <span class="bs {{ $product->stock > 0 ? 'bs-stock-ok' : 'bs-stock-out' }}">
                            {{ $product->stock }} units
                        </span>
                    </td>
                    <td>
                        @if($product->is_featured)
                            <i class="fas fa-star" style="color:#f59e0b"></i>
                        @else
                            <i class="far fa-star" style="color:var(--muted)"></i>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-icon btn-icon-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.products.delete', $product->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Remove this item?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $products->links() }}
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-box"></i>
        <p>No products yet</p>
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
        beforeDraw(chart){
            const {ctx}=chart; ctx.save(); ctx.globalCompositeOperation='destination-over';
            ctx.fillStyle='#fff'; ctx.fillRect(0,0,chart.width,chart.height); ctx.restore();
        }
    };
    const byCategory   = @json($productsByCategory);
    const stockData    = @json($stockData);
    const modal        = new bootstrap.Modal(document.getElementById('chartModal'));
    const titleEl      = document.getElementById('chartModalTitle');
    const subEl        = document.getElementById('chartModalSub');
    const palette      = ['#3b82f6','#06b6d4','#10b981','#f59e0b','#f97316','#ef4444','#8b5cf6','#ec4899'];
    let activeChart = null, currentKey = null;
    const chartDefs = {
        byCategory:{
            title:'📦 Products by Category',sub:'Number of products per category',
            build(ctx){
                const labels=Object.keys(byCategory), data=Object.values(byCategory);
                return new Chart(ctx,{type:'bar',data:{labels,datasets:[{label:'Products',data,backgroundColor:labels.map((_,i)=>palette[i%palette.length]),borderRadius:6,borderSkipped:false}]},plugins:[whiteBgPlugin],options:{indexAxis:'y',responsive:true,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.parsed.x} products`}}},scales:{x:{beginAtZero:true,grid:{color:'#f1f5f9'},ticks:{stepSize:1}},y:{grid:{display:false}}}}});
            }
        },
        inStock:{
            title:'✅ Stock Status',sub:'In-stock vs out-of-stock products',
            build(ctx){
                return new Chart(ctx,{type:'doughnut',data:{labels:['In Stock','Out of Stock'],datasets:[{data:[stockData.in,stockData.out],backgroundColor:['#10b981','#ef4444'],borderWidth:3,borderColor:'#fff',hoverOffset:8}]},plugins:[whiteBgPlugin],options:{cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:16}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed} products`}}}}});
            }
        },
        outOfStock:{
            title:'❌ Out of Stock Products',sub:'Count vs total stock',
            build(ctx){
                return new Chart(ctx,{type:'doughnut',data:{labels:['In Stock','Out of Stock'],datasets:[{data:[stockData.in,stockData.out],backgroundColor:['#10b981','#ef4444'],borderWidth:3,borderColor:'#fff',hoverOffset:8}]},plugins:[whiteBgPlugin],options:{cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:16}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed} products`}}}}});
            }
        },
        featured:{
            title:'⭐ Featured vs Regular Products',sub:'How many products are featured',
            build(ctx){
                return new Chart(ctx,{type:'doughnut',data:{labels:['Featured','Regular'],datasets:[{data:[stockData.featured,stockData.regular],backgroundColor:['#f59e0b','#94a3b8'],borderWidth:3,borderColor:'#fff',hoverOffset:8}]},plugins:[whiteBgPlugin],options:{cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:16}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed} products`}}}}});
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
        const a=document.createElement('a');a.href=o.toDataURL('image/png');a.download=(currentKey||'chart')+'-products.png';a.click();
    });
})();
</script>
@endsection