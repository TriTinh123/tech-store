@extends('layouts.app')
@section('page_title', 'Confirm Payment')
@section('content')
<div class="container py-4">
<style>
/* ─── Progress strip ─────────────────────────────────────── */
.steps-strip{display:flex;gap:0;margin-bottom:28px;border-radius:14px;overflow:hidden;border:1px solid var(--border);box-shadow:0 2px 8px rgba(0,0,0,.05);}
.step-s{flex:1;padding:12px 6px;text-align:center;font-size:11px;font-weight:600;background:#f8fafc;color:var(--text-m);border-right:1px solid var(--border);}
.step-s:last-child{border-right:none;}
.step-s.done{background:#00b894;color:#fff;}
.step-s.current{background:#dbeafe;color:#1e40af;}
/* ─── Cards ──────────────────────────────────────────────── */
.pc-card{background:#fff;border-radius:18px;border:1px solid var(--border);overflow:hidden;margin-bottom:18px;box-shadow:0 2px 12px rgba(0,0,0,.05);}
.pc-head{padding:18px 22px;font-weight:700;font-size:15px;display:flex;align-items:center;gap:12px;border-bottom:1px solid var(--border);}
/* ─── Copy button ────────────────────────────────────────── */
.copy-btn{background:#f4f7fa;border:1.5px solid var(--border);border-radius:8px;padding:5px 12px;font-size:12px;cursor:pointer;color:var(--text-m);transition:.15s;white-space:nowrap;}
.copy-btn:hover{background:#00b894;color:#fff;border-color:#00b894;}
.copy-btn.copied{background:#d1fae5;color:#065f46;border-color:#6ee7b7;}
/* ─── Reference box ──────────────────────────────────────── */
.ref-box{background:linear-gradient(135deg,#667eea,#764ba2);border-radius:14px;padding:20px 22px;color:#fff;text-align:center;margin:16px 0;}
.ref-code{font-size:20px;font-weight:800;letter-spacing:3px;word-break:break-all;}
/* ─── QR wrapper ─────────────────────────────────────────── */
.qr-outer{background:#fff;border-radius:16px;padding:4px;display:inline-block;box-shadow:0 4px 20px rgba(0,0,0,.12);}
.qr-outer img{display:block;width:220px;height:220px;border-radius:12px;object-fit:contain;}
/* ─── Amount badge ───────────────────────────────────────── */
.amount-badge{display:inline-flex;align-items:center;gap:8px;background:#fef9c3;border:1.5px solid #fde047;border-radius:10px;padding:10px 20px;font-size:22px;font-weight:800;color:#92400e;}
/* ─── Steps list ─────────────────────────────────────────── */
.step-num{width:28px;height:28px;background:#00b894;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;}
.step-li{display:flex;gap:12px;align-items:flex-start;padding:10px 0;border-bottom:1px solid var(--border);font-size:14px;color:#374151;}
.step-li:last-of-type{border-bottom:none;}
/* ─── Confirm button ─────────────────────────────────────── */
.btn-done{display:block;width:100%;padding:16px;background:linear-gradient(135deg,#00b894,#00cec9);color:#fff;border:none;border-radius:14px;font-size:16px;font-weight:800;cursor:pointer;transition:.2s;box-shadow:0 4px 14px rgba(0,184,148,.35);}
.btn-done:hover{opacity:.9;transform:translateY(-1px);}
.btn-change{display:block;width:100%;padding:12px;background:#fff;color:var(--text-m);border:2px solid var(--border);border-radius:12px;font-size:14px;font-weight:600;text-align:center;text-decoration:none;transition:.2s;margin-top:10px;}
.btn-change:hover{border-color:#00b894;color:#00b894;}
/* ─── Order summary rows ─────────────────────────────────── */
.info-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:14px;}
.info-row:last-of-type{border-bottom:none;}
/* ─── Wallet gradient cards ──────────────────────────────── */
.wallet-header{border-radius:14px;padding:22px;text-align:center;color:#fff;margin-bottom:20px;}
/* ─── App icon row ───────────────────────────────────────── */
.app-icon-row{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;padding:12px 0;}
.app-icon{width:36px;height:36px;border-radius:8px;object-fit:contain;background:#f8fafc;border:1px solid var(--border);padding:4px;}
</style>

{{-- Progress --}}
<div class="steps-strip mb-4">
    <div class="step-s done"><i class="fas fa-check me-1"></i>Cart</div>
    <div class="step-s done"><i class="fas fa-check me-1"></i>Shipping</div>
    <div class="step-s done"><i class="fas fa-check me-1"></i>Payment</div>
    <div class="step-s current"><i class="fas fa-lock me-1"></i>Confirm</div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">

    {{-- LEFT -- Payment Details --}}
    <div class="col-lg-7">

        @if($gateway === 'bank_transfer')
        {{-- BANK TRANSFER --}}
        <div class="pc-card">
            <div class="pc-head" style="background:linear-gradient(135deg,#dbeafe,#eff6ff)">
                <div style="width:44px;height:44px;background:#1d4ed8;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-university" style="color:#fff;font-size:18px"></i>
                </div>
                <div>
                    <div style="font-size:16px">Bank Transfer</div>
                    <div style="font-size:12px;font-weight:400;color:#3b82f6">TPBank · VietQR — Amount pre-filled in QR</div>
                </div>
                <span class="ms-auto badge" style="background:#1d4ed8;font-size:11px;padding:5px 10px;border-radius:20px">
                    <i class="fas fa-shield-alt me-1"></i>Secure
                </span>
            </div>
            <div class="px-4 py-4">
                <div class="text-center mb-4">
                    <p class="text-muted mb-3" style="font-size:13px">
                        <i class="fas fa-info-circle me-1 text-primary"></i>
                        Open any banking app and scan — amount &amp; transfer note are pre-filled automatically.
                    </p>
                    <div class="qr-outer mx-auto">
                        <img src="{{ asset('/images/tpbank.jpg') }}"
                             alt="TPBank QR"
                             style="width:280px;height:auto;max-width:100%;border-radius:12px;object-fit:contain"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div style="display:none;width:280px;height:280px;background:#f8fafc;border-radius:12px;align-items:center;justify-content:center;color:#9ca3af;font-size:12px;text-align:center;padding:20px">
                            QR image not found
                        </div>
                    </div>
                    <div class="app-icon-row mt-3">
                        @foreach(['MoMo','ZaloPay','TPBank','Vietcombank','MBBank','Techcom','ACB','BIDV'] as $app)
                        <span style="display:inline-block;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;padding:4px 8px;font-size:11px;font-weight:600;color:#475569">{{ $app }}</span>
                        @endforeach
                    </div>
                    <p class="text-muted" style="font-size:11px;margin-top:6px">Works with 50+ banking apps via VietQR / Napas 247</p>
                </div>
                <div class="text-center mb-3">
                    <div class="amount-badge mx-auto">
                        <i class="fas fa-coins" style="color:#92400e"></i>
                        <span>{{ number_format($paymentDetails['amount']) }}&nbsp;₫</span>
                    </div>
                </div>
                <div class="ref-box">
                    <div style="font-size:12px;opacity:.75;margin-bottom:6px;text-transform:uppercase;letter-spacing:1px">
                        Transfer Description (must match exactly)
                    </div>
                    <div class="ref-code" id="refCode">{{ $paymentDetails['reference'] }}</div>
                    <button class="copy-btn mt-3"
                            style="background:rgba(255,255,255,.18);color:#fff;border-color:rgba(255,255,255,.35)"
                            id="copyRefBtn"
                            onclick="copyText('{{ $paymentDetails['reference'] }}','copyRefBtn')">
                        <i class="fas fa-copy me-1"></i>Copy description
                    </button>
                </div>
                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#eff6ff;border:1.5px solid #bfdbfe">
                    <i class="fas fa-building-columns" style="color:#1d4ed8;font-size:22px"></i>
                    <div>
                        <div style="font-size:13px;color:#1e40af;font-weight:700">{{ $paymentDetails['bank_label'] }}</div>
                        <div style="font-size:12px;color:#3b82f6">Domestic &amp; International banking transfers accepted</div>
                    </div>
                </div>
                @if(!empty($paymentDetails['note']))
                <div class="mt-3 p-3 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:13px;color:#14532d">
                    <i class="fas fa-lightbulb me-2 text-success"></i>{{ $paymentDetails['note'] }}
                </div>
                @endif
            </div>
        </div>
        <div class="pc-card">
            <div class="pc-head"><i class="fas fa-list-ol text-success"></i>How to pay</div>
            <div class="px-4 py-2">
                @foreach($paymentDetails['steps'] ?? ['Open your banking app','Tap Scan QR or Transfer','Scan the QR code above — amount is pre-filled','Enter the transfer description if asked','Confirm and submit payment'] as $idx => $st)
                <div class="step-li">
                    <div class="step-num">{{ $idx + 1 }}</div>
                    <span>{{ $st }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <form action="{{ route('checkout.payment.confirm-transfer', $order) }}" method="POST">
            @csrf
            <button type="submit" class="btn-done" onclick="return confirm('Confirm you have completed the bank transfer?')">
                <i class="fas fa-check-circle me-2"></i>I've Completed the Transfer
            </button>
        </form>

        @elseif($gateway === 'momo')
        {{-- MOMO --}}
        <div class="pc-card">
            <div class="pc-head" style="background:linear-gradient(135deg,#fce7f3,#fdf2f8)">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#c2185b,#e91e63);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-mobile-alt" style="color:#fff;font-size:18px"></i>
                </div>
                <div>
                    <div style="font-size:16px">MoMo Wallet</div>
                    <div style="font-size:12px;font-weight:400;color:#be185d">Scan QR code with the MoMo app</div>
                </div>
                <span class="ms-auto badge" style="background:#e91e63;font-size:11px;padding:5px 10px;border-radius:20px">
                    <i class="fas fa-bolt me-1"></i>Instant
                </span>
            </div>
            <div class="px-4 py-4 text-center">
                <div class="wallet-header mx-auto mb-3" style="background:linear-gradient(135deg,#c2185b,#e91e63);max-width:360px;padding:20px;border-radius:14px">
                    <div style="color:#fff;font-size:13px;opacity:.85;margin-bottom:10px;font-weight:600">Scan with MoMo app</div>
                    <div class="qr-outer mx-auto">
                        <img src="{{ asset('/images/momo.jpg') }}"
                             alt="MoMo QR"
                             style="width:100%;height:auto;max-width:320px;border-radius:10px;object-fit:contain;display:block"
                             onerror="this.style.display='none'">
                    </div>
                </div>
                <div class="text-center mb-3">
                    <div class="amount-badge mx-auto">
                        <i class="fas fa-coins" style="color:#92400e"></i>
                        <span>{{ number_format($paymentDetails['amount']) }}&nbsp;₫</span>
                    </div>
                </div>
                <div class="ref-box mb-3" style="background:linear-gradient(135deg,#c2185b,#9c27b0)">
                    <div style="font-size:12px;opacity:.75;margin-bottom:6px;text-transform:uppercase;letter-spacing:1px">
                        Transfer Note / Order Reference
                    </div>
                    <div class="ref-code" id="momoRef">{{ $paymentDetails['ref'] }}</div>
                    <button class="copy-btn mt-3"
                            style="background:rgba(255,255,255,.18);color:#fff;border-color:rgba(255,255,255,.35)"
                            id="copyMomoRef"
                            onclick="copyText('{{ $paymentDetails['ref'] }}','copyMomoRef')">
                        <i class="fas fa-copy me-1"></i>Copy reference
                    </button>
                </div>
                @if(!empty($paymentDetails['note']))
                <div class="p-3 rounded-3 text-start" style="background:#fce7f3;border:1px solid #fbcfe8;font-size:13px;color:#831843">
                    <i class="fas fa-info-circle me-2" style="color:#e91e63"></i>{{ $paymentDetails['note'] }}
                </div>
                @endif
            </div>
        </div>
        <div class="pc-card">
            <div class="pc-head"><i class="fas fa-list-ol text-success"></i>How to pay</div>
            <div class="px-4 py-2">
                @foreach($paymentDetails['steps'] ?? ['Open MoMo app','Tap the QR scan icon','Point camera at the QR code above','Enter the exact amount shown','Add the order reference in the note field','Confirm payment'] as $idx => $st)
                <div class="step-li">
                    <div class="step-num" style="background:#e91e63">{{ $idx + 1 }}</div>
                    <span>{{ $st }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <form action="{{ route('checkout.payment.confirm-transfer', $order) }}" method="POST">
            @csrf
            <button type="submit" class="btn-done" style="background:linear-gradient(135deg,#c2185b,#e91e63);box-shadow:0 4px 14px rgba(194,24,91,.35)"
                    onclick="return confirm('Confirm you have paid via MoMo?')">
                <i class="fas fa-check-circle me-2"></i>I've Paid via MoMo
            </button>
        </form>

        @elseif($gateway === 'zalopay')
        {{-- ZALOPAY --}}
        <div class="pc-card">
            <div class="pc-head" style="background:linear-gradient(135deg,#e0f2fe,#f0f9ff)">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#0369a1,#0ea5e9);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-qrcode" style="color:#fff;font-size:18px"></i>
                </div>
                <div>
                    <div style="font-size:16px">ZaloPay Wallet</div>
                    <div style="font-size:12px;font-weight:400;color:#0369a1">Scan QR code with the ZaloPay app</div>
                </div>
                <span class="ms-auto badge" style="background:#0284c7;font-size:11px;padding:5px 10px;border-radius:20px">
                    <i class="fas fa-bolt me-1"></i>Instant
                </span>
            </div>
            <div class="px-4 py-4 text-center">
                <div class="wallet-header mx-auto mb-3" style="background:linear-gradient(135deg,#0369a1,#06b6d4);max-width:360px;padding:20px;border-radius:14px">
                    <div style="color:#fff;font-size:13px;opacity:.85;margin-bottom:10px;font-weight:600">Scan with ZaloPay app</div>
                    <div class="qr-outer mx-auto">
                        <img src="{{ asset('/images/zalopay.jpg') }}"
                             alt="ZaloPay QR"
                             style="width:100%;height:auto;max-width:320px;border-radius:10px;object-fit:contain;display:block"
                             onerror="this.style.display='none'">
                    </div>
                </div>
                <div class="text-center mb-3">
                    <div class="amount-badge mx-auto">
                        <i class="fas fa-coins" style="color:#92400e"></i>
                        <span>{{ number_format($paymentDetails['amount']) }}&nbsp;₫</span>
                    </div>
                </div>
                <div class="ref-box mb-3" style="background:linear-gradient(135deg,#0369a1,#06b6d4)">
                    <div style="font-size:12px;opacity:.75;margin-bottom:6px;text-transform:uppercase;letter-spacing:1px">
                        Transfer Note / Order Reference
                    </div>
                    <div class="ref-code" id="zaloRef">{{ $paymentDetails['ref'] }}</div>
                    <button class="copy-btn mt-3"
                            style="background:rgba(255,255,255,.18);color:#fff;border-color:rgba(255,255,255,.35)"
                            id="copyZaloRef"
                            onclick="copyText('{{ $paymentDetails['ref'] }}','copyZaloRef')">
                        <i class="fas fa-copy me-1"></i>Copy reference
                    </button>
                </div>
                @if(!empty($paymentDetails['note']))
                <div class="p-3 rounded-3 text-start" style="background:#e0f2fe;border:1px solid #bae6fd;font-size:13px;color:#0c4a6e">
                    <i class="fas fa-info-circle me-2" style="color:#0284c7"></i>{{ $paymentDetails['note'] }}
                </div>
                @endif
            </div>
        </div>
        <div class="pc-card">
            <div class="pc-head"><i class="fas fa-list-ol text-success"></i>How to pay</div>
            <div class="px-4 py-2">
                @foreach($paymentDetails['steps'] ?? ['Open ZaloPay on your phone','Tap the QR scan icon','Scan the QR code above','Enter the exact amount shown','Add the order reference in the note field','Confirm payment'] as $idx => $st)
                <div class="step-li">
                    <div class="step-num" style="background:#0284c7">{{ $idx + 1 }}</div>
                    <span>{{ $st }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <form action="{{ route('checkout.payment.confirm-transfer', $order) }}" method="POST">
            @csrf
            <button type="submit" class="btn-done" style="background:linear-gradient(135deg,#0369a1,#06b6d4);box-shadow:0 4px 14px rgba(3,105,161,.35)"
                    onclick="return confirm('Confirm you have paid via ZaloPay?')">
                <i class="fas fa-check-circle me-2"></i>I've Paid via ZaloPay
            </button>
        </form>

        @elseif($gateway === 'cod')
        {{-- COD --}}
        <div class="pc-card">
            <div class="pc-head" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7)">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#16a34a,#22c55e);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-hand-holding-usd" style="color:#fff;font-size:18px"></i>
                </div>
                <div>
                    <div style="font-size:16px">Cash on Delivery</div>
                    <div style="font-size:12px;font-weight:400;color:#16a34a">Pay in cash when your order arrives</div>
                </div>
            </div>
            <div class="px-4 py-4">
                <div class="text-center mb-4">
                    <div style="width:80px;height:80px;background:linear-gradient(135deg,#16a34a,#22c55e);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                        <i class="fas fa-truck" style="color:#fff;font-size:32px"></i>
                    </div>
                    <h5 class="fw-bold">Your order is confirmed!</h5>
                    <p class="text-muted" style="font-size:14px">You will pay <strong>{{ number_format($paymentDetails['amount'] ?? $order->total_amount) }}&nbsp;₫</strong> in cash when the delivery arrives.</p>
                </div>
                <div class="p-3 rounded-3" style="background:#f0fdf4;border:1.5px solid #bbf7d0;font-size:13px;color:#14532d">
                    <i class="fas fa-lightbulb me-2 text-success"></i>
                    {{ $paymentDetails['note'] ?? 'Please have the exact amount ready. Our delivery staff will provide a receipt.' }}
                </div>
            </div>
        </div>
        <form action="{{ route('checkout.payment.confirm-transfer', $order) }}" method="POST">
            @csrf
            <button type="submit" class="btn-done" style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 4px 14px rgba(22,163,74,.35)">
                <i class="fas fa-check-circle me-2"></i>Confirm Order
            </button>
        </form>

        @else
        {{-- FALLBACK --}}
        <div class="pc-card">
            <div class="pc-head"><i class="fas fa-info-circle text-success"></i>Payment Info</div>
            <div class="px-4 py-4 text-center text-muted">
                <p>{{ $paymentDetails['description'] ?? 'Please contact support for payment assistance.' }}</p>
            </div>
        </div>
        <form action="{{ route('checkout.payment.confirm-transfer', $order) }}" method="POST">
            @csrf
            <button type="submit" class="btn-done">
                <i class="fas fa-check-circle me-2"></i>Confirm Payment
            </button>
        </form>
        @endif

        <a href="{{ route('checkout.payment.method', $order) }}" class="btn-change">
            <i class="fas fa-exchange-alt me-2"></i>Change payment method
        </a>
    </div>

    {{-- RIGHT -- Order Summary --}}
    <div class="col-lg-5">
        <div class="pc-card">
            <div class="pc-head">
                <i class="fas fa-receipt text-success"></i>
                Order&nbsp;#{{ $order->order_number ?? $order->id }}
                @php
                $badgeMap = [
                    'bank_transfer' => ['TPBank · VietQR', '#1d4ed8'],
                    'momo'          => ['MoMo Wallet',     '#e91e63'],
                    'zalopay'       => ['ZaloPay',          '#0284c7'],
                    'cod'           => ['Cash on Delivery', '#16a34a'],
                ];
                $badge = $badgeMap[$gateway] ?? [$gateway, '#64748b'];
                @endphp
                <span class="ms-auto badge" style="background:{{ $badge[1] }};color:#fff;font-size:11px;padding:5px 10px;border-radius:20px;font-weight:600">
                    {{ $badge[0] }}
                </span>
            </div>
            <div class="px-4 py-2">
                <div class="info-row">
                    <span class="text-muted">Customer</span>
                    <span class="fw-bold">{{ $order->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span class="text-muted">Phone</span>
                    <span>{{ $order->customer_phone }}</span>
                </div>
                <div class="info-row">
                    <span class="text-muted">Delivery address</span>
                    <span style="max-width:58%;text-align:right;font-size:12px">{{ $order->delivery_address }}</span>
                </div>
                @if($order->notes)
                <div class="info-row">
                    <span class="text-muted">Notes</span>
                    <span style="font-size:12px;max-width:60%;text-align:right">{{ $order->notes }}</span>
                </div>
                @endif
                @if($order->items && $order->items->count())
                <div class="mt-2 mb-1 fw-bold" style="font-size:12px;color:var(--text-m)">Items ({{ $order->items->count() }})</div>
                @foreach($order->items as $oi)
                <div class="info-row">
                    <span style="flex:1;font-size:13px">
                        {{ Str::limit($oi->product->name ?? 'Product', 28) }}
                        <span class="text-muted">&times;{{ $oi->quantity }}</span>
                    </span>
                    <span class="fw-bold" style="color:#e53e3e;font-size:14px">
                        {{ number_format($oi->subtotal) }}&nbsp;₫
                    </span>
                </div>
                @endforeach
                @endif
                @if($order->coupon_discount ?? 0)
                <div class="info-row">
                    <span class="text-muted">Discount</span>
                    <span style="color:#16a34a;font-weight:600">-{{ number_format($order->coupon_discount) }}&nbsp;₫</span>
                </div>
                @endif
                <div class="d-flex justify-content-between mt-3 pt-3" style="border-top:2.5px solid var(--border);font-size:20px;font-weight:800;color:#e53e3e">
                    <span>Total</span>
                    <span>{{ number_format($order->total_amount ?? 0) }}&nbsp;₫</span>
                </div>
            </div>
        </div>

        @if($gateway !== 'cod')
        <div class="p-3 rounded-3 text-center mb-3" style="background:#fef9c3;border:1.5px solid #fde047;font-size:13px;color:#78350f">
            <i class="fas fa-clock me-1 text-warning"></i>
            <strong>Complete payment within <span id="timer">23:59:00</span></strong><br>
            <span style="font-size:12px">Order will be auto-cancelled if unpaid</span>
        </div>
        @endif

        <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid var(--border);font-size:13px">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-shield-alt text-success"></i>
                <span>SSL 256-bit encrypted &amp; secure</span>
            </div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-undo text-success"></i>
                <span>Free returns within 30 days</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-headset text-success"></i>
                <span>Support: 1900-1234 (8am–10pm daily)</span>
            </div>
        </div>
    </div>
</div>
</div>

<script>
function copyText(text, btnId) {
    var btn = document.getElementById(btnId);
    var orig = btn.innerHTML;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(ok, fallback);
    } else { fallback(); }
    function ok() {
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
        btn.classList.add('copied');
        setTimeout(function() { btn.innerHTML = orig; btn.classList.remove('copied'); }, 2000);
    }
    function fallback() {
        var ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        ok();
    }
}
(function () {
    var el = document.getElementById('timer');
    if (!el) return;
    var s = 23 * 3600 + 59 * 60;
    setInterval(function () {
        if (s <= 0) { el.textContent = '00:00:00'; return; }
        s--;
        var h = Math.floor(s / 3600), m = Math.floor((s % 3600) / 60), sec = s % 60;
        el.textContent = (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m + ':' + (sec < 10 ? '0' : '') + sec;
    }, 1000);
})();
</script>
@endsection