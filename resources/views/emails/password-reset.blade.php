@component('mail::message')
# Password Reset — TechStore

Hello {{ $user->name }},

We received a request to reset the password for your account. Click the button below to reset your password:

@component('mail::button', ['url' => $resetLink, 'color' => 'success'])
Reset Password
@endcomponent

This link will expire in 60 minutes.

If you did not request a password reset, please ignore this email.

Best regards,<br>
{{ config('app.name') }}

@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
@endcomponent
@endcomponent
