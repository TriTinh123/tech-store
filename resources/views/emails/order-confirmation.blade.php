@component('mail::message')
# Xác Nhận Đặt Hàng

Cảm ơn bạn đã đặt hàng tại cửa hàng của chúng tôi!

## Chi Tiết Đơn Hàng
**Mã Đơn Hàng:** #{{ $order->id }}  
**Ngày Đặt:** {{ $order->created_at->format('d/m/Y H:i') }}  
**Trạng Thái:** {{ ucfirst($order->status) }}  
**Tổng Tiền:** {{ number_format($order->total_amount, 0, ',', '.') }}₫  

@if($order->coupon_code)
**Mã Giảm Giá:** {{ $order->coupon_code }}  
**Tiền Giảm:** {{ number_format($order->discount_amount, 0, ',', '.') }}₫  
@endif

## Sản Phẩm Đặt Hàng

@if($items && count($items) > 0)
@foreach($items as $item)
- **{{ $item->product_name ?? $item->name }}** ({{ $item->quantity }}x) - {{ number_format($item->subtotal, 0, ',', '.') }}₫
@endforeach
@else
Đơn hàng không có sản phẩm
@endif

## Thông Tin Giao Hàng
**Họ Tên:** {{ $order->customer_name }}  
**Địa Chỉ:** {{ $order->delivery_address }}  
**Điện Thoại:** {{ $order->customer_phone }}  

## Tiếp Theo
Đơn hàng của bạn đang được chuẩn bị. Chúng tôi sẽ cập nhật thông tin vận chuyển cho bạn trong thời gian sớm nhất.

Bạn có thể theo dõi đơn hàng tại: 
@component('mail::button', ['url' => route('order.success', $order)])
Xem Chi Tiết Đơn Hàng
@endcomponent

Nếu có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.

Cảm ơn,  
{{ config('app.name') }}
@endcomponent
