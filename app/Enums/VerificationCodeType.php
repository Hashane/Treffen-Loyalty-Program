<?php

namespace App\Enums;

enum VerificationCodeType: string
{
    case PasswordReset = 'password_reset';
    case EmailVerification = 'email_verification';
    case TwoFactor = '2fa';
    case PhoneVerification = 'phone_verification';
}
