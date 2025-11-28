@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
<div style="font-family: 'Nunito', Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #1a202c;">
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="display: inline-block; margin-bottom: 20px;">
            <div style="padding: 12px; background: linear-gradient(135deg, #0369a1 0%, #0e7490 100%); border-radius: 16px; transform: rotate(6deg);">
                <div style="background: white; padding: 8px; border-radius: 12px; transform: rotate(-6deg);">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height: 48px; width: auto;">
                </div>
            </div>
        </div>
        <h1 style="color: #1a202c; font-size: 24px; font-weight: 700; margin: 0 0 10px 0;">
            {{ __('auth.reset_password_email.title', [], $locale) }}
        </h1>
        <p style="color: #4a5568; font-size: 16px; margin: 0 0 30px 0;">
            {{ __('auth.reset_password_email.subtitle', [], $locale) }}
        </p>
    </div>

    <div style="background-color: #f8fafc; border-radius: 12px; padding: 30px; margin-bottom: 30px;">
        <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
            {{ __('auth.reset_password_email.you_are_receiving', [], $locale) }}
        </p>
        
        @component('mail::button', [
            'url' => route('password.reset', [
                'token' => $token,
                'email' => $email,
                'locale' => $locale
            ]),
            'color' => 'primary'
        ])
            {{ __('auth.reset_password_email.reset_button', [], $locale) }}
        @endcomponent
        
        <p style="color: #4a5568; font-size: 14px; line-height: 1.6; margin: 20px 0 0 0;">
            {{ __('auth.reset_password_email.expiry_notice', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')], $locale) }}
        </p>
    </div>

    <div style="text-align: center; margin-bottom: 30px;">
        <p style="color: #4a5568; font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;">
            {{ __('auth.reset_password_email.ignore_if_not_requested', [], $locale) }}
        </p>
        
        <p style="color: #4a5568; font-size: 14px; line-height: 1.6; margin: 0;">
            {{ __('auth.reset_password_email.contact_support', ['email' => config('mail.support_email')], $locale) }}
        </p>
    </div>
</div>

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
    <p style="color: #718096; font-size: 12px; text-align: center; margin: 0;">
        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
    </p>
    
    @component('mail::subcopy')
        {{ __('auth.reset_password_email.trouble_with_button', [], $locale) }}
        <br>
        <a href="{{ route('password.reset', ['token' => $token, 'email' => $email, 'locale' => $locale]) }}" style="color: #3182ce; word-break: break-all;">
            {{ route('password.reset', ['token' => $token, 'email' => $email, 'locale' => $locale]) }}
        </a>
    @endcomponent
@endcomponent
@endslot
@endcomponent
