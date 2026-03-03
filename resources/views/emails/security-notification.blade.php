@component('mail::message')
{{-- Header --}}
# {{ $notification->getSeverityIcon() }} {{ $notification->title }}

{{-- Severity Badge --}}
@switch($notification->severity)
    @case('critical')
        <div style="background-color: #dc2626; color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center; font-weight: bold;">
            🚨 CRITICAL ALERT
        </div>
        @break
    @case('warning')
        <div style="background-color: #ea580c; color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center; font-weight: bold;">
            ⚠️ WARNING
        </div>
        @break
    @default
        <div style="background-color: #3b82f6; color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center; font-weight: bold;">
            ℹ️ INFORMATION
        </div>
@endswitch

{{-- Message --}}
<p>{{ $notification->message }}</p>

{{-- Details --}}
@if($notification->details)
    <div style="background-color: #f3f4f6; padding: 15px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
        <strong>Chi tiết:</strong>
        <ul style="margin: 10px 0;">
            @foreach($notification->details as $key => $value)
                <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ is_array($value) ? implode(', ', $value) : $value }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Action Button --}}
@if($notification->action_url && $notification->action_label)
@component('mail::button', ['url' => $notification->action_url, 'color' => $notification->severity === 'critical' ? 'error' : 'primary'])
{{ $notification->action_label }}
@endcomponent
@endif

{{-- Footer Info --}}
<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280;">
    <p><strong>Thông tin tài khoản:</strong></p>
    <ul style="margin: 5px 0;">
        <li>Người dùng: {{ $user->name }} ({{ $user->email }})</li>
        <li>Loại sự kiện: {{ $notification->type }}</li>
        <li>Thời gian: {{ $notification->created_at->format('d/m/Y H:i:s') }}</li>
    </ul>
</div>

{{-- Security Notice --}}
<div style="background-color: #fef3c7; border: 1px solid #fcd34d; border-radius: 4px; padding: 12px; margin-top: 20px;">
    <strong>🔒 Lưu ý bảo mật:</strong> Nếu bạn không thực hiện hành động này, vui lòng kiểm tra tài khoản của bạn ngay lập tức. Không chia sẻ liên kết hoặc mã này với bất kỳ ai.
</div>

{{-- Unsubscribe Info --}}
<p style="margin-top: 30px; font-size: 12px; color: #6b7280;">
    Bạn có thể quản lý tùy chọn thông báo trong <a href="{{ route('profile.notifications.preferences') }}" style="color: #3b82f6;">cài đặt thông báo</a> của bạn.
</p>

@endcomponent
