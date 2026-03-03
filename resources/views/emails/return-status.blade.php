@component('mail::message')
# Cập Nhật Yêu Cầu Hoàn Trả

@if($status === 'approved')
## Yêu Cầu Hoàn Trả Được Phê Duyệt ✓

Chúng tôi đã phê duyệt yêu cầu hoàn trả của bạn. Bạn sẽ nhận được hoàn tiền trong 3-5 ngày làm việc.

@elseif($status === 'rejected')
## Yêu Cầu Hoàn Trả Bị Từ Chối ✗

Rất tiếc, yêu cầu hoàn trả của bạn không được phê duyệt.

@if($return->rejection_reason)
**Lý Do:** {{ $return->rejection_reason }}
@endif

@else
## Cập Nhật Yêu Cầu Hoàn Trả

Yêu cầu hoàn trả của bạn đã được xử lý.

@endif

## Chi Tiết Hoàn Trả
**Đơn Hàng:** #{{ $return->order->id }}  
**Sản Phẩm:** {{ $return->orderItem->product->name }}  
**Số Lượng:** {{ $return->orderItem->quantity }}  
**Lý Do:** 
@switch($return->reason)
    @case('defective')
        Sản phẩm bị lỗi
        @break
    @case('wrong_item')
        Nhận được sản phẩm sai
        @break
    @case('not_as_described')
        Không giống mô tả
        @break
    @case('changed_mind')
        Thay đổi ý kiến
        @break
    @default
        Khác: {{ $return->reason }}
@endswitch

**Số Tiền Hoàn:** {{ number_format($return->refund_amount, 0, ',', '.') }}₫  
**Ngày Yêu Cầu:** {{ $return->created_at->format('d/m/Y H:i') }}  

@if($status === 'completed')
**Ngày Hoàn Thành:** {{ $return->completed_at->format('d/m/Y H:i') }}
@endif

## Tiếp Theo
@component('mail::button', ['url' => route('returns.show', $return)])
Xem Chi Tiết Hoàn Trả
@endcomponent

Nếu có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.

Cảm ơn,  
{{ config('app.name') }}
@endcomponent
