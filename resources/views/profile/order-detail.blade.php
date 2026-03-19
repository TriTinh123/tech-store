@extends('layouts.app')

@section('page_title', 'Track Order #' . ($order->order_number ?? $order->id))

@section('content')
@php
    $statusMap = [
        'pending'   => ['index' => 0, 'label' => 'Pending confirmation',    'color' => '#f39c12', 'bg' => '#fff9ec', 'icon' => 'fa-clock'],
        'confirmed' => ['index' => 1, 'label' => 'Confirmed',     'color' => '#0984e3', 'bg' => '#e8f4fd', 'icon' => 'fa-check'],
        'shipped'   => ['index' => 2, 'label' => 'Shipping',  'color' => '#6c5ce7', 'bg' => '#f0eeff', 'icon' => 'fa-truck'],
        'delivered' => ['index' => 3, 'label' => 'Delivered','color'=> '#00b894', 'bg' => '#e6faf5', 'icon' => 'fa-check-circle'],
        'cancelled' => ['index' => -1,'label' => 'Cancelled',      'color' => '#e84040', 'bg' => '#ffeaea', 'icon' => 'fa-times-circle'],
    ];
    $cs       = $statusMap[$order->status] ?? $statusMap['pending'];
    $idx      = $cs['index'];
    $t        = $order->created_at;
    $eta      = $t->copy()->addDays(3)->format('d/m/Y');
    $steps = [
        ['label'  => 'Order placed',
         'sub'    => 'Order <strong>#' . ($order->order_number ?? $order->id) . '</strong> has been received',
         'time'   => $t->format('H:i – d/m/Y'),
         'done'   => true,
         'active' => $idx === 0,
         'icon'   => 'fa-shopping-bag',
         'color'  => '#00b894'],
        ['label'  => 'Order confirmed',
         'sub'    => 'Seller confirmed and preparing the order',
         'time'   => $t->copy()->addHours(1)->format('H:i – d/m/Y'),
         'done'   => $idx >= 1,
         'active' => $idx === 1,
         'icon'   => 'fa-clipboard-check',
         'color'  => '#0984e3'],
        ['label'  => 'Packing',
         'sub'    => 'Order is being carefully packed',
         'time'   => $t->copy()->addHours(4)->format('H:i – d/m/Y'),
         'done'   => $idx >= 1,
         'active' => false,
         'icon'   => 'fa-box',
         'color'  => '#fdcb6e'],
        ['label'  => 'Handed to shipping carrier',
         'sub'    => $order->tracking_number ? 'Tracking number: <strong>' . $order->tracking_number . '</strong>' . ($order->shipping_provider ? ' – ' . $order->shipping_provider : '') : 'Package is in transit',
         'time'   => $t->copy()->addDays(1)->format('H:i – d/m/Y'),
         'done'   => $idx >= 2,
         'active' => $idx === 2,
         'icon'   => 'fa-truck',
         'color'  => '#6c5ce7'],
        ['label'  => 'Delivering to you',
         'sub'    => 'Shipper is on the way to your address',
         'time'   => $t->copy()->addDays(2)->format('H:i – d/m/Y'),
         'done'   => $idx >= 2,
         'active' => false,
         'icon'   => 'fa-motorcycle',
         'color'  => '#e17055'],
        ['label'  => 'Delivered successfully',
         'sub'    => 'You have received your order. Thank you for shopping!',
         'time'   => ($idx >= 3) ? $order->updated_at->format('H:i – d/m/Y') : 'Est. ' . $eta,
         'done'   => $idx >= 3,
         'active' => $idx === 3,
         'icon'   => 'fa-home',
         'color'  => '#00b894'],
    ];
    if ($order->status === 'cancelled') {
        $steps = [
            ['label'  => 'Order placed',
             'sub'    => 'Order <strong>#' . ($order->order_number ?? $order->id) . '</strong> has been received',
             'time'   => $t->format('H:i – d/m/Y'),
             'done'   => true, 'active' => false,
             'icon'   => 'fa-shopping-bag', 'color' => '#636e72'],
            ['label'  => 'Order cancelled',
             'sub'    => 'Order was cancelled per request',
             'time'   => $order->updated_at->format('H:i – d/m/Y'),
             'done'   => true, 'active' => true,
             'icon'   => 'fa-times-circle', 'color' => '#e84040'],
        ];
    }
    $payMethod = ['cod' => ['l' => 'Cash on delivery','i' => 'fa-money-bill-wave','c' => '#f39c12'],
                  'bank_transfer' => ['l' => 'Bank Transfer','i' => 'fa-university','c' => '#0984e3'],
                  'momo'    => ['l' => 'MoMo Wallet','i' => 'fa-wallet','c' => '#d63031'],
                  'zalopay' => ['l' => 'ZaloPay','i' => 'fa-wallet','c' => '#0096e6']];
    $pm = $payMethod[$order->payment_method] ?? $payMethod[$order->payment_gateway ?? ''] ?? ['l' => ucfirst($order->payment_method ?? 'Unknown'),'i' => 'fa-credit-card','c' => '#636e72'];
@endphp

<div class="od-wrap">
  {{-- ── STATUS HERO ── --}}
  @if($order->status !== 'cancelled')
  <div class="od-hero" style="background:{{ $cs['bg'] }}; border-left:5px solid {{ $cs['color'] }}">
    <div class="od-hero-left">
      <div class="od-status-icon" style="background:{{ $cs['color'] }}">
        <i class="fas {{ $cs['icon'] }}"></i>
      </div>
      <div>
        <div class="od-status-title" style="color:{{ $cs['color'] }}">{{ $cs['label'] }}</div>
        @if($order->status !== 'delivered')
        <div class="od-status-sub">Estimated delivery: <strong>{{ $eta }}</strong></div>
        @else
        <div class="od-status-sub" style="color:#00b894">Delivered {{ $order->updated_at->format('d/m/Y') }}</div>
        @endif
      </div>
    </div>
    <div class="od-order-meta">
      <span class="od-meta-num">#{{ $order->order_number ?? $order->id }}</span>
      <span class="od-meta-date">{{ $order->created_at->format('d/m/Y') }}</span>
    </div>
  </div>
  {{-- Progress bar --}}
  <div class="od-progress-bar">
    @php $pct = [0=>8, 1=>35, 2=>65, 3=>100]; $w = $pct[$idx] ?? 0; @endphp
    <div class="od-progress-fill" style="width:{{ $w }}%"></div>
    @foreach(['Place Order','Confirm','Shipping','Received'] as $si => $sl)
      <div class="od-progress-dot {{ $idx >= $si ? 'done' : '' }} {{ $idx === $si ? 'active' : '' }}">
        <div class="od-pdot-circle">{{ $si < $idx ? '✓' : ($si+1) }}</div>
        <div class="od-pdot-label">{{ $sl }}</div>
      </div>
    @endforeach
  </div>
  @else
  {{-- Cancelled hero --}}
  <div class="od-hero" style="background:#ffeaea; border-left:5px solid #e84040">
    <div class="od-hero-left">
      <div class="od-status-icon" style="background:#e84040"><i class="fas fa-times-circle"></i></div>
      <div>
        <div class="od-status-title" style="color:#e84040">Order cancelled</div>
        <div class="od-status-sub">Cancelled at {{ $order->updated_at->format('H:i, d/m/Y') }}</div>
      </div>
    </div>
    <div class="od-order-meta">
      <span class="od-meta-num">#{{ $order->order_number ?? $order->id }}</span>
    </div>
  </div>
  @endif

  <div class="od-body">
    {{-- ── TRACKING TIMELINE ── --}}
    <div class="od-card">
      <div class="od-card-head">
        <i class="fas fa-map-marker-alt"></i> Order Status
        @if($order->tracking_number)
        <span class="od-tracking-badge">
          <i class="fas fa-barcode"></i> {{ $order->tracking_number }}
        </span>
        @endif
      </div>
      <div class="od-timeline">
        @foreach($steps as $i => $step)
        <div class="od-step {{ $step['done'] ? 'done' : '' }} {{ $step['active'] ? 'step-active' : '' }}">
          <div class="od-step-line-wrap">
            <div class="od-step-dot" style="{{ $step['done'] ? 'background:'.$step['color'].';border-color:'.$step['color'] : '' }}">
              @if($step['done'])
                <i class="fas {{ $step['active'] ? $step['icon'] : 'fa-check' }}"></i>
              @else
                <i class="fas {{ $step['icon'] }}"></i>
              @endif
            </div>
            @if($i < count($steps)-1)
            <div class="od-step-connector {{ $step['done'] && $steps[$i+1]['done'] ? 'filled' : '' }}"></div>
            @endif
          </div>
          <div class="od-step-content">
            <div class="od-step-label {{ $step['active'] ? 'active-label' : '' }}"
                 style="{{ $step['active'] ? 'color:'.$step['color'] : '' }}">
              {{ $step['label'] }}
              @if($step['active'])<span class="od-step-badge" style="background:{{ $step['color'] }}">Current</span>@endif
            </div>
            <div class="od-step-sub">{!! $step['sub'] !!}</div>
            <div class="od-step-time">
              <i class="fas fa-clock"></i> {{ $step['time'] }}
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <div class="od-side-grid">
      {{-- ── ITEMS ── --}}
      <div class="od-card">
        <div class="od-card-head"><i class="fas fa-box-open"></i> Items in order ({{ $order->items->count() }})</div>
        @foreach($order->items as $item)
        <div class="od-item-row">
          @if(isset($item->product->image))
          <img src="{{ asset('storage/'.$item->product->image) }}" class="od-item-img" alt="{{ $item->product->name ?? '' }}"
               onerror="this.src='https://via.placeholder.com/60x60/f4f7fa/94a3b8?text=IMG'">
          @else
          <div class="od-item-img-ph"><i class="fas fa-image"></i></div>
          @endif
          <div class="od-item-info">
            <div class="od-item-name">{{ $item->product->name ?? 'Products' }}</div>
            <div class="od-item-meta">x{{ $item->quantity }} • ₫{{ number_format($item->price, 0, ',', '.') }}</div>
          </div>
          <div class="od-item-total">₫{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</div>
        </div>
        @endforeach
        <div class="od-item-summary">
          @if($order->discount_amount > 0)
          <div class="od-summary-row"><span>Savings</span><span style="color:#00b894">-₫{{ number_format($order->discount_amount, 0, ',', '.') }}</span></div>
          @endif
          <div class="od-summary-row"><span>Shipping</span><span style="color:#00b894">Free</span></div>
          <div class="od-summary-row total-row"><span>Total</span><span>₫{{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
        </div>
      </div>

      {{-- ── DELIVERY INFO ── --}}
      <div class="od-card">
        <div class="od-card-head"><i class="fas fa-map-marker-alt"></i> Delivery Address</div>
        <div class="od-info-block">
          <div class="od-info-row"><i class="fas fa-user"></i><span>{{ $order->customer_name }}</span></div>
          <div class="od-info-row"><i class="fas fa-phone"></i><span>{{ $order->customer_phone }}</span></div>
          <div class="od-info-row"><i class="fas fa-envelope"></i><span>{{ $order->customer_email }}</span></div>
          <div class="od-info-row"><i class="fas fa-home"></i><span>{{ $order->delivery_address }}</span></div>
          @if($order->notes)
          <div class="od-info-row"><i class="fas fa-sticky-note"></i><span>{{ $order->notes }}</span></div>
          @endif
        </div>
      </div>

      {{-- ── PAYMENT INFO ── --}}
      <div class="od-card">
        <div class="od-card-head"><i class="fas fa-credit-card"></i> Payment Info</div>
        <div class="od-info-block">
          <div class="od-info-row">
            <i class="fas {{ $pm['i'] }}" style="color:{{ $pm['c'] }}"></i>
            <span>{{ $pm['l'] }}</span>
          </div>
          <div class="od-info-row">
            <i class="fas fa-check-circle" style="color:{{ $order->payment_status === 'paid' ? '#00b894' : '#f39c12' }}"></i>
            <span>
              @if($order->payment_status === 'paid')
                <span style="color:#00b894;font-weight:600">Paid</span>
                @if($order->paid_at) • {{ $order->paid_at->format('d/m/Y H:i') }}@endif
              @else
                <span style="color:#f39c12;font-weight:600">Unpaid</span>
              @endif
            </span>
          </div>
          @if($order->payment_reference)
          <div class="od-info-row"><i class="fas fa-hashtag"></i><span>Transaction ID: {{ $order->payment_reference }}</span></div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- ── ACTIONS ── --}}
  <div class="od-actions">
    <a href="{{ route('orders.index') }}" class="od-btn od-btn-ghost">
      <i class="fas fa-arrow-left"></i> My Orders
    </a>
    <a href="{{ route('home') }}" class="od-btn od-btn-outline">
      <i class="fas fa-shopping-bag"></i> Continue shopping
    </a>
    @if(in_array($order->status, ['delivered','completed']))
      @php $hasReturn = \App\Models\OrderReturn::where('order_id', $order->id)->exists(); @endphp
      @if(!$hasReturn)
      <a href="{{ route('orders.return.create', $order->id) }}" class="od-btn od-btn-warn">
        <i class="fas fa-undo-alt"></i> Return Request
      </a>
      @else
      <span class="od-btn od-btn-disabled"><i class="fas fa-check"></i> Return request submitted</span>
      @endif
    @endif
  </div>
</div>

<style>
:root{--green:#00b894;--blue:#0984e3;--text:#1a1f2e;--text-m:#64748b;--border:#e8edf2;--bg:#f4f7fa}
.od-wrap{max-width:960px;margin:32px auto;padding:0 16px}

/* Hero */
.od-hero{display:flex;align-items:center;justify-content:space-between;gap:16px;background:#e6faf5;border-left:5px solid var(--green);border-radius:14px;padding:20px 24px;margin-bottom:0;flex-wrap:wrap}
.od-hero-left{display:flex;align-items:center;gap:16px}
.od-status-icon{width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--green);color:#fff;font-size:1.4rem;flex-shrink:0}
.od-status-title{font-size:1.2rem;font-weight:700;color:var(--text)}
.od-status-sub{font-size:.9rem;color:var(--text-m);margin-top:2px}
.od-order-meta{text-align:right}
.od-meta-num{display:block;font-weight:700;color:var(--text);font-size:1.05rem}
.od-meta-date{font-size:.85rem;color:var(--text-m)}

/* Progress bar */
.od-progress-bar{display:flex;align-items:flex-start;justify-content:space-between;position:relative;padding:28px 24px 20px;background:#fff;border-radius:0 0 14px 14px;margin-bottom:20px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden}
.od-progress-bar::before{content:'';position:absolute;top:36px;left:calc(24px + 20px);right:calc(24px + 20px);height:4px;background:var(--border);border-radius:2px;z-index:0}
.od-progress-fill{position:absolute;top:36px;left:calc(24px + 20px);height:4px;background:linear-gradient(90deg,var(--green),var(--blue));border-radius:2px;z-index:1;transition:width .6s ease}
.od-progress-dot{display:flex;flex-direction:column;align-items:center;gap:6px;z-index:2;position:relative}
.od-pdot-circle{width:40px;height:40px;border-radius:50%;border:3px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;color:#b2bec3;transition:all .3s}
.od-progress-dot.done .od-pdot-circle{background:var(--green);border-color:var(--green);color:#fff}
.od-progress-dot.active .od-pdot-circle{background:var(--blue);border-color:var(--blue);color:#fff;box-shadow:0 0 0 6px rgba(9,132,227,.15);transform:scale(1.1)}
.od-pdot-label{font-size:.75rem;color:var(--text-m);white-space:nowrap;font-weight:500}
.od-progress-dot.done .od-pdot-label{color:var(--green);font-weight:600}
.od-progress-dot.active .od-pdot-label{color:var(--blue);font-weight:700}

/* Body layout */
.od-body{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
.od-body > .od-card:first-child{grid-column:1/-1}

/* Card */
.od-card{background:#fff;border-radius:14px;padding:22px 24px;box-shadow:0 2px 12px rgba(0,0,0,.06)}
.od-card-head{font-size:1rem;font-weight:700;color:var(--text);border-bottom:2px solid var(--border);padding-bottom:14px;margin-bottom:18px;display:flex;align-items:center;gap:8px}
.od-card-head i{color:var(--green)}
.od-tracking-badge{margin-left:auto;background:#f0f4ff;color:var(--blue);font-size:.8rem;padding:4px 10px;border-radius:20px;font-weight:600}

/* Timeline */
.od-timeline{padding:4px 0}
.od-step{display:flex;gap:0;position:relative}
.od-step-line-wrap{display:flex;flex-direction:column;align-items:center;width:48px;flex-shrink:0}
.od-step-dot{width:40px;height:40px;border-radius:50%;border:3px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;font-size:.95rem;color:#b2bec3;transition:all .3s;flex-shrink:0}
.od-step.done .od-step-dot{color:#fff}
.od-step.step-active .od-step-dot{box-shadow:0 0 0 6px rgba(9,132,227,.12);transform:scale(1.08)}
.od-step-connector{flex:1;width:3px;background:var(--border);min-height:32px;transition:background .3s}
.od-step-connector.filled{background:var(--green)}
.od-step-content{padding:2px 0 28px 16px;flex:1}
.od-step:last-child .od-step-content{padding-bottom:4px}
.od-step-label{font-size:.97rem;font-weight:600;color:#b2bec3;margin-bottom:4px;display:flex;align-items:center;gap:8px}
.od-step.done .od-step-label{color:var(--text)}
.active-label{font-weight:700!important}
.od-step-badge{font-size:.7rem;padding:2px 8px;border-radius:12px;color:#fff;font-weight:600}
.od-step-sub{font-size:.85rem;color:var(--text-m);margin-bottom:4px;line-height:1.4}
.od-step.done .od-step-sub{color:var(--text-m)}
.od-step-time{font-size:.78rem;color:#adb5bd;display:flex;align-items:center;gap:4px}
.od-step.done .od-step-time{color:var(--text-m)}

/* Items */
.od-item-row{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)}
.od-item-row:last-of-type{ border-bottom:none}
.od-item-img{width:60px;height:60px;object-fit:cover;border-radius:10px;flex-shrink:0}
.od-item-img-ph{width:60px;height:60px;background:#f4f7fa;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#b2bec3;flex-shrink:0}
.od-item-info{flex:1}
.od-item-name{font-size:.92rem;font-weight:600;color:var(--text);line-height:1.3}
.od-item-meta{font-size:.82rem;color:var(--text-m);margin-top:2px}
.od-item-total{font-size:.92rem;font-weight:700;color:var(--text);white-space:nowrap}
.od-item-summary{border-top:2px solid var(--border);padding-top:14px;margin-top:10px}
.od-summary-row{display:flex;justify-content:space-between;font-size:.9rem;color:var(--text-m);margin-bottom:6px}
.od-summary-row.total-row{font-size:1.05rem;font-weight:700;color:var(--text);margin-top:4px;padding-top:8px;border-top:1px solid var(--border)}

/* Info block */
.od-info-block{display:flex;flex-direction:column;gap:10px}
.od-info-row{display:flex;align-items:flex-start;gap:10px;font-size:.9rem;color:var(--text)}
.od-info-row i{color:var(--green);width:16px;flex-shrink:0;margin-top:2px}

/* Side grid */
.od-side-grid{display:grid;grid-template-columns:1fr;gap:16px}

/* Actions */
.od-actions{display:flex;gap:12px;flex-wrap:wrap;padding:4px 0 8px}
.od-btn{display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border-radius:10px;font-size:.9rem;font-weight:600;text-decoration:none;cursor:pointer;border:none;transition:all .2s}
.od-btn-ghost{background:#f4f7fa;color:var(--text-m)}
.od-btn-ghost:hover{background:#e8edf2;color:var(--text)}
.od-btn-outline{background:var(--green);color:#fff}
.od-btn-outline:hover{background:#00a381;color:#fff}
.od-btn-warn{background:#fff4e5;color:#f39c12;border:1px solid #ffd080}
.od-btn-warn:hover{background:#f39c12;color:#fff}
.od-btn-disabled{background:#f4f7fa;color:#b2bec3;cursor:default}

@media(max-width:720px){
  .od-body{grid-template-columns:1fr}
  .od-body > .od-card:first-child{grid-column:1/-1}
  .od-progress-bar{padding:20px 12px 16px;overflow-x:auto}
  .od-hero{flex-direction:column;align-items:flex-start}
  .od-order-meta{text-align:left}
}
</style>
@endsection
