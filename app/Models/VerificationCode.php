<?php

namespace App\Models;

use App\Enums\VerificationCodeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class VerificationCode extends Model
{
    protected $fillable = [
        'member_id',
        'identifier',
        'type',
        'code',
        'expires_at',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'type' => VerificationCodeType::class,
            'expires_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return ! $this->isExpired() && $this->attempts < 3;
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    public function verifyCode(string $code): bool
    {
        return Hash::check($code, $this->code);
    }
}
