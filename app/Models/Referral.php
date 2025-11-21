<?php

namespace App\Models;

use App\Enums\Referrals\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_member_id',
        'referred_member_id',
        'referral_code',
        'referred_email',
        'referred_phone',
        'bonus_points_awarded',
        'points_ledger_id',
        'status',
        'invited_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => Status::class,
            'invited_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'referrer_member_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'referred_member_id');
    }

    public function pointsLedger(): BelongsTo
    {
        return $this->belongsTo(PointsLedger::class);
    }
}
