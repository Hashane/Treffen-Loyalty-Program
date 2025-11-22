<?php

namespace App;

use App\Enums\VerificationCodeType;
use App\Models\VerificationCode;

trait VerifiesCode
{
    protected function findAndVerifyCode(string $email, string $code, VerificationCodeType $type): VerificationCode
    {
        $verificationCode = VerificationCode::where('identifier', $email)
            ->where('type', $type)
            ->first();

        if (! $verificationCode) {
            abort(400, 'Invalid or expired verification code.');
        }

        if (! $verificationCode->isValid()) {
            abort(400, 'Verification code has expired or exceeded maximum attempts.');
        }

        if (! $verificationCode->verifyCode($code)) {
            $verificationCode->incrementAttempts();
            abort(400, 'Invalid verification code.');
        }

        return $verificationCode;
    }
}
