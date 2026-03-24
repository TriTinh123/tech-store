@extends('layouts.app')
@section('page_title', 'Select Payment Method')
@section('content')
<div class="container py-4">
<style>
.gw-card { display:flex;align-items:center;padding:16px 18px;border:2.5px solid var(--border);border-radius:14px;cursor:pointer;transition:all .2s;margin-bottom:12px;background:#fff;position:relative; }
.gw-card:hover { box-shadow:0 4px 18px rgba(0,0,0,.09); }
.gw-card.active { border-color:var(--green);background:#f0fdf4;box-shadow:0 4px 18px rgba(0,184,148,.15); }
.gw-card input[type=radio] { margin-right:14px;accent-color:var(--green);width:20px;height:20px;cursor:pointer;flex-shrink:0; }
.gw-logo { width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;margin-right:14px; }
.gw-badge { position:absolute;top:-8px;right:14px;padding:2px 10px;border-radius:10px;font-size:11px;font-weight:700; }
.badge-pop { background:#fee2e2;color:#be123c; }
.badge-fast { background:#fef9c3;color:#854d0e; }
.badge-safe { background:#dbeafe;color:#1e40af; }
.summary-card { background:#fff;border-radius:14px;border:1px solid var(--border);padding:22px;position:sticky;top:90px; }
.s-row { display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px; }
.s-row:last-of-type{border-bottom:none;}
.btn-confirm-pay { display:block;width:100%;padding:15px;background:linear-gradient(135deg,#00b894,#00cec9);color:#fff;border:none;border-radius:12px;font-size:16px;font-weight:800;cursor:pointer;transition:.2s;letter-spacing:.3px; }
.btn-confirm-pay:hover{opacity:.9;}
.steps-strip { display:flex;gap:0;margin-bottom:22px;border-radius:12px;overflow:hidden;border:1px solid var(--border); }
.step-s { flex:1;padding:10px 6px;text-align:center;font-size:11px;font-weight:600;background:#f8fafc;color:var(--text-m);border-right:1px solid var(--border); }
.step-s:last-child{border-right:none;}
.step-s.done { background:var(--green);color:#fff; }
.step-s.current { background:#dbeafe;color:#1e40af; }
</style>

{{-- Progress strip --}}
<div class="steps-strip mb-4">
    <div class="step-s done"><i class="fas fa-check me-1"></i>Info</div>
    <div class="step-s current"><i class="fas fa-check me-1"></i>Place Order</div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    {{-- Left: Gateway Selection --}}
    <div class="col-lg-7">
        <div class="bg-white rounded-4 border p-4" style="border-color:var(--border)!important">
            <h5 class="fw-800 mb-1"><i class="fas fa-credit-card me-2 text-success"></i>Select Payment Method</h5>
            <p class="text-muted mb-4" style="font-size:13px">Order <strong>#{{ $order->id }}</strong> — Choose a payment method, you can change it anytime before confirming.</p>

            <form action="{{ route('checkout.payment.process', $order) }}" method="POST" id="payForm">
                @csrf

                {{-- COD --}}
                <label class="gw-card {{ old('payment_gateway','cod')=='cod'?'active':'' }}" onclick="selectGW(this)">
                    <span class="gw-badge badge-fast">Fastest</span>
                    <input type="radio" name="payment_gateway" value="cod" {{ old('payment_gateway','cod')=='cod'?'checked':'' }} required>
                    <div class="gw-logo" style="background:#fef9c3;color:#ca8a04"><i class="fas fa-truck"></i></div>
                    <div>
                        <div class="fw-700" style="font-size:15px">Cash on Delivery (COD)</div>
                        <div class="text-muted" style="font-size:12px">Pay cash on delivery — no card needed</div>
                    </div>
                </label>

                {{-- Bank Transfer --}}
                <label class="gw-card {{ old('payment_gateway')=='bank_transfer'?'active':'' }}" onclick="selectGW(this)">
                    <span class="gw-badge badge-safe">High Security</span>
                    <input type="radio" name="payment_gateway" value="bank_transfer" {{ old('payment_gateway')=='bank_transfer'?'checked':'' }} required>
                    <div class="gw-logo" style="background:#dbeafe;color:#1d4ed8"><i class="fas fa-university"></i></div>
                    <div>
                        <div class="fw-700" style="font-size:15px">Bank Transfer</div>
                        <div class="text-muted" style="font-size:12px">Vietcombank · ACB · MB Bank — Supports VietQR</div>
                    </div>
                </label>

                {{-- Momo --}}
                <label class="gw-card {{ old('payment_gateway')=='momo'?'active':'' }}" onclick="selectGW(this)">
                    <span class="gw-badge badge-pop">Popular</span>
                    <input type="radio" name="payment_gateway" value="momo" {{ old('payment_gateway')=='momo'?'checked':'' }} required>
                    <div class="gw-logo" style="background:#fce7f3;color:#be185d"><i class="fas fa-mobile-alt"></i></div>
                    <div>
                        <div class="fw-700" style="font-size:15px">Momo Wallet</div>
                        <div class="text-muted" style="font-size:12px">Scan QR — Fast and convenient</div>
                    </div>
                </label>

                {{-- ZaloPay --}}
                <label class="gw-card {{ old('payment_gateway')=='zalopay'?'active':'' }}" onclick="selectGW(this)">
                    <input type="radio" name="payment_gateway" value="zalopay" {{ old('payment_gateway')=='zalopay'?'checked':'' }} required>
                    <div class="gw-logo" style="background:#e0f2fe;color:#0369a1"><i class="fas fa-qrcode"></i></div>
                    <div>
                        <div class="fw-700" style="font-size:15px">ZaloPay</div>
                        <div class="text-muted" style="font-size:12px">Scan QR via ZaloPay — Integrated with Zalo</div>
                    </div>
                </label>

                {{-- Security note --}}
                <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4" style="background:#f4f7fa;font-size:12px;color:var(--text-m)">
                    <i class="fas fa-lock text-success fa-lg"></i>
                    <span>All transactions are SSL 256-bit encrypted. Your payment info is completely secure.</span>
                </div>

                <button type="submit" class="btn-confirm-pay" id="submitBtn">
                    <i class="fas fa-shield-check me-2"></i><span id="btnLabel">Pay Now</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Right: Order Summary --}}
    <div class="col-lg-5">
        <div class="summary-card">
            <h6 class="fw-700 mb-3"><i class="fas fa-receipt me-2 text-success"></i>Order #{{ $order->id }}</h6>
            <div class="s-row"><span class="text-muted">Customer</span><span class="fw-600">{{ $order->customer_name ?? ($order->user->name ?? 'N/A') }}</span></div>
            <div class="s-row"><span class="text-muted">Deliver to</span><span style="max-width:55%;text-align:right;font-size:12px">{{ $order->delivery_address ?? 'N/A' }}</span></div>

            @if($order->items && $order->items->count())
            <div class="mt-2 mb-1 text-muted" style="font-size:12px;font-weight:600">Products:</div>
            @foreach($order->items as $oi)
            <div class="s-row">
                <span style="flex:1">{{ Str::limit($oi->product->name??'SP',28) }} <span class="text-muted">x{{ $oi->quantity }}</span></span>
                <span class="fw-600" style="color:var(--danger)">${{ number_format($oi->subtotal, 2) }}</span>
            </div>
            @endforeach
            @endif

            <div class="d-flex justify-content-between mt-3 pt-3" style="border-top:2px solid var(--border);font-size:20px;font-weight:800;color:var(--danger)">
                <span>Total</span>
                <span>${{ number_format($order->total_amount ?? 0, 2) }}</span>
            </div>

            <div class="mt-3 p-3 rounded-3" style="background:#fef9c3;border:1px dashed #fde047;font-size:12px;color:#78350f">
                <i class="fas fa-info-circle me-1"></i>
                Wrong choice? Just click another option and press <strong>Pay Now</strong> again.
            </div>
        </div>
    </div>
</div>
</div>

<script>
const methodLabels = {
    cod:           'Place Order COD — Pay on delivery',
    bank_transfer: 'Continue — Bank Transfer',
    momo:          'Continue — Pay via Momo',
    zalopay:       'Continue — ZaloPay',
};
function selectGW(el) {
    document.querySelectorAll('.gw-card').forEach(e=>e.classList.remove('active'));
    el.classList.add('active');
    const r = el.querySelector('input[type=radio]');
    r.checked = true;
    document.getElementById('btnLabel').textContent = methodLabels[r.value] || 'Pay Now';
}
// init label
document.addEventListener('DOMContentLoaded', function(){
    const checked = document.querySelector('input[name=payment_gateway]:checked');
    if(checked) document.getElementById('btnLabel').textContent = methodLabels[checked.value]||'Pay Now';
});
</script>
@endsection