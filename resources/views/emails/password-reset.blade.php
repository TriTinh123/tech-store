@component('mail::message')
# Đặt lại mật khẩu TechStore

Xin chào {{ $user->name }},

Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Nhấp vào nút dưới đây để đặt lại mật khẩu:

@component('mail::button', ['url' => $resetLink, 'color' => 'success'])
Đặt lại mật khẩu
@endcomponent

Link này sẽ hết hạn trong 60 phút.

Nếu bạn không yêu cầu đặt lại mật khẩu, hãy bỏ qua email này.

Trân trọng,<br>
{{ config('app.name') }}

@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. Tất cả các quyền được bảo lưu.
@endcomponent
@endcomponent
