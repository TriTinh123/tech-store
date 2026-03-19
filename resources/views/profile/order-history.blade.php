@extends('layouts.app')

@section('page_title', 'My Orders')

@section('content')
@php
    $statusCfg = [
        'pending'   => ['label' => 'Pending confirmation',    'color' => '#f39c12', 'bg' => '#fff9ec','icon' => 'fa-clock',       'pct' => 10],
        'confirmed' => ['label' => 'Confirmed',     'color' => '#0984e3', 'bg' => '#e8f4fd','icon' => 'fa-check',       'pct' => 35],
        'shipped'   => ['label' => 'Shipping',  'color' => '#6c5ce7', 'bg' => '#f0eeff','icon' => 'fa-truck',       'pct' => 70],
        'delivered' => ['label' => 'Delivered','color'=> '#00b894', 'bg' => '#e6faf5','icon' => 'fa-check-circle','pct' => 100],
        'cancelled' => ['label' => 'Cancelled',      'color' => '#e84040', 'bg' => '#ffeaea','icon' => 'fa-times-circle','pct' => 0],
    ];
@endphp

<div class="oh-wrap">

  {{-- Top bar --}}
  <div class="oh-topbar">
    <div>
      <h1 class="oh-page-title"><i class="fas fa-shopping-bag"></i> My Orders</h1>
      <p class="oh-page-sub">Track and manage all your orders</p>
    </div>
    <a href="{{ route('home') }}" class="oh-btn-shop"><i class="fas fa-plus"></i> Shop more</a>
  </div>

  @if(session('success'))
  <div class="oh-alert-ok"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
  @endif

  @if($orders->count() == 0)
  <div class="oh-empty">
    <div class="oh-empty-icon"><i class="fas fa-box-open"></i></div>
    <div class="oh-empty-title">No orders yet</div>
    <div class="oh-empty-sub">Explore and place your first order!</div>
    <a href="{{ route('home') }}" class="oh-btn-primary"><i class="fas fa-shopping-cart"></i> Shop now</a>
  </div>
  @else

  {{-- Order cards --}}
  @foreach($orders as $order)
  @php $sc = $statusCfg[$order->status] ?? $statusCfg['pending']; @endphp
  <div class="oh-order-card">
    {{-- Card header --}}
    <div class="oh-card-head">
      <div class="oh-card-meta">
        <span class="oh-order-num">#{{ $order->order_number ?? $order->id }}</span>
        <span class="oh-order-date"><i class="fas fa-calendar-alt"></i> {{ $order->created_at->format('d/m/Y H:i') }}</span>
        <span class="oh-order-count"><i class="fas fa-box"></i> {{ $order->items_count ?? $order->items()->count() }} products</span>
      </div>
      <span class="oh-status-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['color'] }}33">
        <i class="fas {{ $sc['icon'] }}"></i> {{ $sc['label'] }}
      </span>
    </div>

    {{-- Progress strip --}}
    @if($order->status !== 'cancelled')
    <div class="oh-progress-strip">
      <div class="oh-ps-track">
        <div class="oh-ps-fill" style="width:{{ $sc['pct'] }}%;background:{{ $sc['color'] }}"></div>
        @foreach(['Place Order','Confirm','Shipping','Received'] as $pi => $pl)
          @php $pPct = [0=>0,1=>33,2=>66,3=>100][$pi]; $done = $sc['pct'] >= $pPct+5; @endphp
          <div class="oh-ps-node {{ $done ? 'done' : '' }}" style="{{ $done ? 'background:'.$sc['color'] : '' }}">
            {{ $done ? '✓' : ($pi + 1) }}
          </div>
        @endforeach
      </div>
      <div class="oh-ps-labels">
        @foreach(['Place Order','Confirm','Shipping','Received'] as $pl)
          <span>{{ $pl }}</span>
        @endforeach
      </div>
    </div>
    @else
    <div class="oh-cancelled-strip">
      <i class="fas fa-ban"></i> Order cancelled on {{ $order->updated_at->format('d/m/Y H:i') }}
    </div>
    @endif

    {{-- Card body --}}
    <div class="oh-card-body">
      {{-- First 2 items preview --}}
      <div class="oh-items-preview">
        @foreach($order->items->take(2) as $item)
        <div class="oh-preview-item">
          @if(isset($item->product->image))
          <img src="{{ asset('storage/'.$item->product->image) }}" class="oh-item-thumb"
               onerror="this.src='https://via.placeholder.com/48x48/f4f7fa/94a3b8?text=IMG'" alt="">
          @else
          <div class="oh-item-thumb-ph"><i class="fas fa-image"></i></div>
          @endif
          <span class="oh-item-name">{{ Str::limit($item->product->name ?? 'Products', 30) }}</span>
          <span class="oh-item-qty">x{{ $item->quantity }}</span>
        </div>
        @endforeach
        @if($order->items->count() > 2)
        <div class="oh-more-items">+{{ $order->items->count() - 2 }} more products</div>
        @endif
      </div>

      {{-- Price & action --}}
      <div class="oh-card-right">
        @if($order->status !== 'delivered')
        <div class="oh-eta">
          <i class="fas fa-clock"></i>
          @if($order->status === 'cancelled') Cancelled
          @elseif($order->status === 'shipped') Est. {{ $order->created_at->addDays(3)->format('d/m/Y') }}
          @else In progress
          @endif
        </div>
        @else
        <div class="oh-eta delivered"><i class="fas fa-check-circle"></i> Delivered {{ $order->updated_at->format('d/m/Y') }}</div>
        @endif
        <div class="oh-total">${{ number_format($order->total_amount, 2) }}</div>
        <a href="{{ route('profile.order-detail', $order->id) }}" class="oh-btn-detail" style="background:{{ $sc['color'] }}">
          <i class="fas fa-map-marker-alt"></i> Track Order
        </a>
      </div>
    </div>
  </div>
  @endforeach

  {{-- Pagination --}}
  @if($orders->hasPages())
  <div class="oh-pagination">
    @if($orders->onFirstPage())
      <span class="oh-page-btn disabled">← Previous</span>
    @else
      <a class="oh-page-btn" href="{{ $orders->previousPageUrl() }}">← Previous</a>
    @endif
    @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
      @if($page == $orders->currentPage())
        <span class="oh-page-btn active">{{ $page }}</span>
      @else
        <a class="oh-page-btn" href="{{ $url }}">{{ $page }}</a>
      @endif
    @endforeach
    @if($orders->hasMorePages())
      <a class="oh-page-btn" href="{{ $orders->nextPageUrl() }}">Next →</a>
    @else
      <span class="oh-page-btn disabled">Next →</span>
    @endif
  </div>
  @endif

  @endif
</div>

<style>
:root{--green:#00b894;--blue:#0984e3;--text:#1a1f2e;--text-m:#64748b;--border:#e8edf2;--bg:#f4f7fa}
.oh-wrap{max-width:900px;margin:32px auto;padding:0 16px}

/* Top bar */
.oh-topbar{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;gap:12px;flex-wrap:wrap}
.oh-page-title{font-size:1.5rem;font-weight:800;color:var(--text);margin:0;display:flex;align-items:center;gap:10px}
.oh-page-title i{color:var(--green)}
.oh-page-sub{font-size:.9rem;color:var(--text-m);margin:4px 0 0}
.oh-btn-shop{background:var(--green);color:#fff;padding:10px 20px;border-radius:10px;font-weight:600;text-decoration:none;font-size:.9rem;display:inline-flex;align-items:center;gap:8px;transition:background .2s}
.oh-btn-shop:hover{background:#00a381;color:#fff}

/* Alert */
.oh-alert-ok{background:#e6faf5;border:1px solid #00b89433;color:var(--green);padding:12px 18px;border-radius:10px;margin-bottom:20px;font-weight:600;display:flex;align-items:center;gap:8px}

/* Empty */
.oh-empty{background:#fff;border-radius:16px;padding:60px 24px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.06)}
.oh-empty-icon{font-size:3.5rem;color:#e0e7ef;margin-bottom:16px}
.oh-empty-title{font-size:1.2rem;font-weight:700;color:var(--text);margin-bottom:6px}
.oh-empty-sub{color:var(--text-m);margin-bottom:24px}
.oh-btn-primary{background:var(--green);color:#fff;padding:12px 28px;border-radius:10px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px}

/* Order card */
.oh-order-card{background:#fff;border-radius:16px;margin-bottom:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden;transition:box-shadow .2s}
.oh-order-card:hover{box-shadow:0 6px 24px rgba(0,0,0,.1)}

/* Card head */
.oh-card-head{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border);gap:12px;flex-wrap:wrap}
.oh-card-meta{display:flex;align-items:center;gap:14px;flex-wrap:wrap}
.oh-order-num{font-weight:800;color:var(--text);font-size:1rem}
.oh-order-date,.oh-order-count{font-size:.82rem;color:var(--text-m);display:flex;align-items:center;gap:4px}
.oh-status-badge{font-size:.8rem;font-weight:700;padding:5px 12px;border-radius:20px;display:inline-flex;align-items:center;gap:5px;white-space:nowrap}

/* Progress strip */
.oh-progress-strip{padding:16px 20px 8px;background:#fafbfc}
.oh-ps-track{position:relative;display:flex;align-items:center;justify-content:space-between;height:28px}
.oh-ps-track::before{content:'';position:absolute;top:50%;transform:translateY(-50%);left:14px;right:14px;height:4px;background:var(--border);border-radius:2px;z-index:0}
.oh-ps-fill{position:absolute;top:50%;transform:translateY(-50%);left:14px;height:4px;border-radius:2px;z-index:1;transition:width .5s ease;min-width:4px}
.oh-ps-node{width:28px;height:28px;border-radius:50%;border:3px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#b2bec3;z-index:2;position:relative;transition:all .3s}
.oh-ps-node.done{color:#fff;border-color:transparent}
.oh-ps-labels{display:flex;justify-content:space-between;margin-top:6px;padding:0 6px}
.oh-ps-labels span{font-size:.72rem;color:var(--text-m);text-align:center;flex:1}

/* Cancelled strip */
.oh-cancelled-strip{background:#ffeaea;color:#e84040;padding:10px 20px;font-size:.85rem;font-weight:600;display:flex;align-items:center;gap:8px}

/* Card body */
.oh-card-body{display:flex;align-items:flex-end;justify-content:space-between;padding:16px 20px;gap:16px;flex-wrap:wrap}
.oh-items-preview{flex:1;display:flex;flex-direction:column;gap:8px}
.oh-preview-item{display:flex;align-items:center;gap:10px}
.oh-item-thumb{width:44px;height:44px;object-fit:cover;border-radius:8px;flex-shrink:0}
.oh-item-thumb-ph{width:44px;height:44px;background:var(--bg);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#b2bec3;flex-shrink:0}
.oh-item-name{font-size:.85rem;color:var(--text);flex:1}
.oh-item-qty{font-size:.8rem;color:var(--text-m);white-space:nowrap}
.oh-more-items{font-size:.8rem;color:var(--text-m);margin-left:54px}

/* Card right */
.oh-card-right{display:flex;flex-direction:column;align-items:flex-end;gap:10px;flex-shrink:0}
.oh-eta{font-size:.8rem;color:var(--text-m);display:flex;align-items:center;gap:5px}
.oh-eta.delivered{color:var(--green);font-weight:600}
.oh-total{font-size:1.15rem;font-weight:800;color:var(--text)}
.oh-btn-detail{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;color:#fff;font-size:.85rem;font-weight:700;text-decoration:none;transition:opacity .2s}
.oh-btn-detail:hover{opacity:.85;color:#fff}

/* Pagination */
.oh-pagination{display:flex;justify-content:center;gap:6px;flex-wrap:wrap;margin-top:28px}
.oh-page-btn{padding:8px 14px;border-radius:8px;background:#fff;border:1px solid var(--border);color:var(--text-m);font-size:.88rem;text-decoration:none;transition:all .2s}
.oh-page-btn:hover{background:var(--green);color:#fff;border-color:var(--green)}
.oh-page-btn.active{background:var(--green);color:#fff;border-color:var(--green);font-weight:700}
.oh-page-btn.disabled{opacity:.45;cursor:default;pointer-events:none}

@media(max-width:600px){
  .oh-card-body{flex-direction:column;align-items:flex-start}
  .oh-card-right{align-items:flex-start;width:100%}
  .oh-btn-detail{width:100%;justify-content:center}
  .oh-card-head{flex-direction:column;align-items:flex-start}
}
</style>
@endsection
