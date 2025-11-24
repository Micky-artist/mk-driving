@component('mail::message')
# {{ __('Reset Password Notification') }}

{{ __('You are receiving this email because we received a password reset request for your account.') }}

@component('mail::button', ['url' => $resetUrl])
{{ __('Reset Password') }}
@endcomponent

{{ __('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]) }}

{{ __('If you did not request a password reset, no further action is required.') }}

{{ __('Regards') }},<br>
{{ config('app.name') }}

@component('mail::subcopy')
{{ __("If you're having trouble clicking the \"Reset Password\" button, copy and paste the URL below into your web browser:") }}

[{{ $resetUrl }}]({{ $resetUrl }})
@endcomponent
@endcomponent
