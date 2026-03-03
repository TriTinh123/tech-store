@component('mail::message')
# Cập Nhật Vận Chuyển

Đơn hàng của bạn đang được vận chuyển!

## Thông Tin Vận Chuyển
**Mã Đơn Hàng:** #{{ $order->id }}  
**Nhà Vận Chuyển:** {{ $order->shipping_provider ?? 'Chưa xác định' }}  
**Mã Theo Dõi:** {{ $order->tracking_number ?? 'Chưa cập nhật' }}  
**Trạng Thái:** 
@switch($order->shipping_status)
    @case('pending')
        Chờ xử lý
        @break
    @case('processing')
        Đang chuẩn bị
        @break
    @case('shipped')
        Đã gửi
        @break
    @case('out_for_delivery')
        Đang giao
        @break
    @case('delivered')
        Đã giao
        @break
    @case('returned')
        Trả lại
        @break
    @default
        {{ ucfirst($order->shipping_status) }}
@endswitch

**Cập Nhật Lúc:** {{ $order->updated_at->format('d/m/Y H:i') }}

## Thông Tin Sản Phẩm
@foreach($order->items as $item)
- **{{ $item->product->name }}** ({{ $item->quantity }}x) - {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫
@endforeach

**Tổng Tiền:** {{ number_format($order->total_amount, 0, ',', '.') }}₫

## Theo Dõi Đơn Hàng
@component('mail::button', ['url' => route('orders.show', $order)])
Xem Trạng Thái Đơn Hàng
@endcomponent

Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.

Cảm ơn,  
{{ config('app.name') }}
@endcomponent
