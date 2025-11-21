<?php

namespace App\Models;

use App\Enums\OtpVerifications\Channel;
use App\Enums\OtpVerifications\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'redemption_id',
        'otp_code',
        'phone_or_email',
        'channel',
        'attempts',
        'max_attempts',
        'verified_at',
        'status',
        'sent_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'channel' => Channel::class,
            'status' => Status::class,
            'verified_at' => 'datetime',
            'sent_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function redemption(): BelongsTo
    {
        return $this->belongsTo(Redemption::class);
    }
}
