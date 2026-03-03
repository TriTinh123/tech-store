{{-- @var \App\Models\User $user --}}
@php
    /** @var \App\Models\User $user */
@endphp
<x-mail::message>
# Three-Factor Authentication Code

Hello {{ $user->name }},

Your 3FA verification code is:

<x-mail::panel>
{{ $otp }}
</x-mail::panel>

This code will expire in {{ $expiresIn }} minutes.

If you did not request this code, please ignore this email and ensure your account is secure.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
