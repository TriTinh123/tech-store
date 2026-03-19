@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm" style="border-radius:12px;overflow:hidden;border:none">
                <div class="card-header" style="background:linear-gradient(135deg,#667eea,#764ba2);padding:18px 24px;border:none">
                    <h5 class="mb-0" style="color:#fff"><i class="fas fa-undo-alt me-2"></i>Return Requests / Refunds</h5>
                    <p class="mb-0 mt-1" style="color:rgba(255,255,255,.7);font-size:13px">Order #{{ $order->order_number }}</p>
                </div>
                <div class="card-body p-4">

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif

                    {{-- Order summary --}}
                    <div style="background:#f8fafc;border-radius:8px;padding:14px 16px;margin-bottom:20px;border:1px solid #e2e8f0">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:12px;color:#6b7280">Order ID</span>
                            <strong style="font-size:13px">#{{ $order->order_number }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:12px;color:#6b7280">Total</span>
                            <strong style="font-size:13px;color:#e74c3c">${{ number_format($order->total_amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span style="font-size:12px;color:#6b7280">Order Date</span>
                            <span style="font-size:12px">{{ $order->created_at?->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <form action="{{ route('orders.return.store', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px">Request type <span style="color:#ef4444">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="return_type" id="type_refund" value="refund" checked>
                                    <label class="form-check-label" for="type_refund" style="font-size:13px">💰 Refund</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="return_type" id="type_exchange" value="exchange">
                                    <label class="form-check-label" for="type_exchange" style="font-size:13px">🔄 Exchange</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="font-size:13px">Return reason <span style="color:#ef4444">*</span></label>
                            <textarea name="reason" class="form-control" rows="5" required minlength="10" maxlength="1000"
                                placeholder="Detailed description your reason for the return or refund (at least 10 characters)..."
                                style="resize:none;font-size:13px">{{ old('reason') }}</textarea>
                            <div style="font-size:11px;color:#9ca3af;margin-top:4px">Minimum 10 characters. The more detail you provide, the faster we can process your request.</div>
                        </div>

                        <div style="background:#fffbeb;border:1px solid #fed7aa;border-radius:8px;padding:12px 14px;margin-bottom:20px;font-size:12px;color:#92400e">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Note:</strong> We accept return requests within <strong>7 days</strong> of receiving the item. Products must be unused and in original condition.
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-secondary flex-fill">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-paper-plane me-1"></i> Submit request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
