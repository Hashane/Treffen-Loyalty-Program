@component('mail::message')

    # Password Reset

    Hello {{ $member->first_name }},

    You recently requested to reset your password.
    Use the verification code below to complete the process:

    @component('mail::panel')
        **Verification Code:**
        # {{ $otpCode }}
    @endcomponent

    This code will expire in **10 minutes**.
    If you didn’t request a password reset, please ignore this email or contact support.

    Thanks,
    {{ config('app.name') }}

    @slot('subcopy')
        This is an automated message, please do not reply.
    @endslot

@endcomponent
