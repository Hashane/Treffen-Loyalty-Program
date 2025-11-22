@component('mail::message')

# Email Verification

Hello {{ $member->first_name }},

Thank you for registering with {{ config('app.name') }}.
Please verify your email address using the verification code below:
/Users/judehashane/Herd/treffen-loyalty/resources/views/emails/password-reset-otp.blade.php
@component('mail::panel')
  **Verification Code:**
  # {{ $otpCode }}
@endcomponent

This code will expire in **10 minutes**.
If you didn't create an account, please ignore this email or contact support.

Thanks,
{{ config('app.name') }}

@slot('subcopy')
  This is an automated message, please do not reply.
@endslot

@endcomponent
