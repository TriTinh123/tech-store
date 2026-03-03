{{-- @var \App\Models\User $user --}}
@component('mail::message')
@php
    /** @var \App\Models\User $user */
@endphp
# ⚠️ Cảnh báo An ninh Tài khoản

Xin chào {{ $user->name }},

{{ $alert->message }}

## Chi tiết Cảnh báo
- **Loại cảnh báo:** {{ $alertTypeLabel }}
- **Mức độ:** <span style="color: {{ match($severityBadge['color']) { 'red' => '#dc2626', 'orange' => '#ea580c', 'yellow' => '#eab308', 'blue' => '#2563eb', default => '#6b7280' } }}">{{ $severityBadge['label'] }}</span>
- **Thời gian:** {{ $alert->created_at->format('H:i:s d/m/Y') }}

@if($alert->suspiciousLogin)
## Thông tin Đăng nhập
- **Địa chỉ IP:** {{ $alert->suspiciousLogin->ip_address }}
- **Thiết bị:** {{ $alert->suspiciousLogin->user_agent }}
- **Vị trí:** {{ $alert->suspiciousLogin->location ?? 'Không xác định' }}
@endif

## Hành động Khuyến cáo
1. **Nếu đây là bạn:** Bỏ qua cảnh báo này. Tài khoản của bạn an toàn.
2. **Nếu đây không phải là bạn:** 
   - Đổi mật khẩu ngay lập tức
   - Chọn lại các câu hỏi bảo mật
   - Liên hệ với chúng tôi nếu có bất kỳ vấn đề gì

@component('mail::button', ['url' => route('profile.security')])
Quản lý An ninh Tài khoản
@endcomponent

---

**Lưu ý:** TechStore không bao giờ yêu cầu bạn cung cấp mật khẩu hoặc thông tin cá nhân qua email. Nếu bạn nhận được email đáng ngờ, vui lòng báo cáo nó.

Cảm ơn,<br>
Đội ngũ TechStore
@endcomponent
