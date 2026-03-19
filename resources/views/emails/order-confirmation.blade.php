<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmation</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fa;font-family:'Segoe UI',Arial,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fa;padding:32px 0">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%">

      {{-- Header --}}
      <tr>
        <td style="background:linear-gradient(135deg,#00b894 0%,#0984e3 100%);border-radius:16px 16px 0 0;padding:32px 40px;text-align:center">
          <div style="font-size:28px;font-weight:800;color:#fff;letter-spacing:-0.5px">🛒 TechStore</div>
          <div style="margin-top:12px">
            <span style="background:rgba(255,255,255,.2);color:#fff;padding:6px 18px;border-radius:20px;font-size:13px;font-weight:600">
              ✅ Order placed
            </span>
          </div>
          <div style="color:rgba(255,255,255,.9);font-size:15px;margin-top:10px">Thank you, <strong>{{ $order->customer_name }}</strong> for trusting us!</div>
        </td>
      </tr>

      {{-- Body --}}
      <tr>
        <td style="background:#fff;padding:32px 40px">

          {{-- Order Number Banner --}}
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:12px;margin-bottom:24px">
            <tr>
              <td style="padding:16px 20px">
                <div style="font-size:13px;color:#0369a1;font-weight:600;margin-bottom:4px">Your Order ID</div>
                <div style="font-size:22px;font-weight:800;color:#0c4a6e">#{{ $order->order_number ?? $order->id }}</div>
                <div style="font-size:12px;color:#64748b;margin-top:4px">Placed at {{ $order->created_at->format('H:i, d/m/Y') }}</div>
              </td>
              <td style="padding:16px 20px;text-align:right">
                <div style="font-size:12px;color:#64748b;margin-bottom:4px">Total Payment</div>
                <div style="font-size:24px;font-weight:800;color:#e84040">{{ number_format($order->total_amount, 0, ',', '.') }}₫</div>
              </td>
            </tr>
          </table>

          {{-- Tracking Timeline --}}
          <div style="margin-bottom:28px">
            <div style="font-size:14px;font-weight:700;color:#1a1f2e;margin-bottom:16px;display:flex;align-items:center;gap:8px">
              📦 Order Journey
            </div>
            <table width="100%" cellpadding="0" cellspacing="0">
              @php
                $steps = [
                  ['icon'=>'✅','label'=>'Order placed','sub'=>'Order has been received','done'=>true,'time'=>$order->created_at->format('H:i, d/m/Y')],
                  ['icon'=>'🏪','label'=>'Order Confirmation','sub'=>'Seller is confirming','done'=>false,'time'=>'Expected within 1–2 hours'],
                  ['icon'=>'📦','label'=>'Pack & ship','sub'=>'Items being prepared','done'=>false,'time'=>'Expected within 4–8 hours'],
                  ['icon'=>'🛵','label'=>'Delivery to you','sub'=>'Est. 3–5 business days','done'=>false,'time'=>$order->created_at->addDays(3)->format('d/m/Y').' – '.$order->created_at->addDays(5)->format('d/m/Y')],
                ];
              @endphp
              @foreach($steps as $step)
              <tr>
                <td style="width:32px;text-align:center;vertical-align:top;padding-bottom:12px">
                  <div style="width:32px;height:32px;border-radius:50%;background:{{ $step['done'] ? '#00b894' : '#e8edf2' }};display:flex;align-items:center;justify-content:center;font-size:14px;line-height:32px;text-align:center">
                    {{ $step['done'] ? '✓' : '○' }}
                  </div>
                </td>
                <td style="padding-left:12px;padding-bottom:12px;border-left:2px solid {{ $step['done'] ? '#00b894' : '#e8edf2' }};">
                  <div style="font-size:13px;font-weight:{{ $step['done'] ? '700' : '500' }};color:{{ $step['done'] ? '#1a1f2e' : '#94a3b8' }}">{{ $step['label'] }}</div>
                  <div style="font-size:12px;color:#94a3b8">{{ $step['sub'] }} • {{ $step['time'] }}</div>
                </td>
              </tr>
              @endforeach
            </table>
          </div>

          {{-- Products --}}
          <div style="margin-bottom:24px">
            <div style="font-size:14px;font-weight:700;color:#1a1f2e;padding-bottom:10px;border-bottom:2px solid #f4f7fa;margin-bottom:12px">🛍️ Ordered Items</div>
            @foreach($items as $item)
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:8px">
              <tr>
                <td style="font-size:13px;color:#1a1f2e;font-weight:600">{{ $item->product_name ?? ($item->product->name ?? 'Products') }}</td>
                <td style="text-align:right;font-size:13px;color:#64748b;white-space:nowrap">x{{ $item->quantity }}</td>
                <td style="text-align:right;font-size:13px;font-weight:700;color:#e84040;white-space:nowrap;padding-left:12px">{{ number_format(($item->subtotal ?? $item->price * $item->quantity), 0, ',', '.') }}₫</td>
              </tr>
            </table>
            @endforeach
            <table width="100%" cellpadding="0" cellspacing="0" style="border-top:2px solid #f4f7fa;margin-top:8px;padding-top:10px">
              @if(($order->discount_amount ?? 0) > 0)
              <tr>
                <td style="font-size:13px;color:#64748b;padding-top:6px">Discount ({{ $order->coupon_code }})</td>
                <td style="text-align:right;font-size:13px;color:#00b894;font-weight:700;padding-top:6px">-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</td>
              </tr>
              @endif
              <tr>
                <td style="font-size:13px;color:#64748b;padding-top:6px">Shipping</td>
                <td style="text-align:right;font-size:13px;color:#00b894;font-weight:700;padding-top:6px">Free</td>
              </tr>
              <tr>
                <td style="font-size:16px;font-weight:800;color:#1a1f2e;padding-top:10px">Total</td>
                <td style="text-align:right;font-size:18px;font-weight:800;color:#e84040;padding-top:10px">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
              </tr>
            </table>
          </div>

          {{-- Delivery Info --}}
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;padding:16px;margin-bottom:24px">
            <tr><td colspan="2" style="font-size:14px;font-weight:700;color:#1a1f2e;padding-bottom:10px">📍 Delivery Address</td></tr>
            <tr>
              <td style="font-size:13px;color:#64748b;padding-bottom:4px;width:120px">Recipient</td>
              <td style="font-size:13px;color:#1a1f2e;font-weight:600;padding-bottom:4px">{{ $order->customer_name }}</td>
            </tr>
            <tr>
              <td style="font-size:13px;color:#64748b;padding-bottom:4px">Phone</td>
              <td style="font-size:13px;color:#1a1f2e;padding-bottom:4px">{{ $order->customer_phone }}</td>
            </tr>
            <tr>
              <td style="font-size:13px;color:#64748b;padding-bottom:4px">Address</td>
              <td style="font-size:13px;color:#1a1f2e;padding-bottom:4px">{{ $order->delivery_address }}</td>
            </tr>
            <tr>
              <td style="font-size:13px;color:#64748b">Payment</td>
              <td style="font-size:13px;color:#1a1f2e">{{ ['cod'=>'Cash on delivery','bank_transfer'=>'Bank transfer','momo'=>'MoMo Wallet','zalopay'=>'ZaloPay'][$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
            </tr>
          </table>

          {{-- CTA Button --}}
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px">
            <tr>
              <td align="center">
                <a href="{{ route('orders.show', $order->id) }}" style="display:inline-block;background:linear-gradient(135deg,#00b894,#0984e3);color:#fff;text-decoration:none;padding:14px 36px;border-radius:10px;font-size:15px;font-weight:700">📦 Track Order</a>
              </td>
            </tr>
          </table>

          <div style="font-size:12px;color:#94a3b8;text-align:center">If you have questions, please contact: <a href="mailto:{{ config('mail.from.address') }}" style="color:#0984e3">{{ config('mail.from.address') }}</a></div>
        </td>
      </tr>

      {{-- Footer --}}
      <tr>
        <td style="background:#1a1f2e;border-radius:0 0 16px 16px;padding:20px 40px;text-align:center">
          <div style="font-size:13px;color:rgba(255,255,255,.5)">© {{ date('Y') }} TechStore • This email was sent automatically, please do not reply</div>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
