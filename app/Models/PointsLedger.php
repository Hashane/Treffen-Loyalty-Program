<?php

namespace App\Models;

use App\Enums\PointsLedger\PointsType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PointsLedger extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'transaction_id',
        'redemption_id',
        'points_change',
        'points_type',
        'current_points_after',
        'lifetime_points_after',
        'expiry_date',
        'expired',
        'description',
        'adjusted_by',
        'adjustment_reason',
    ];

    protected function casts(): array
    {
        return [
            'points_type' => PointsType::class,
            'expiry_date' => 'date',
            'expired' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function redemption(): BelongsTo
    {
        return $this->belongsTo(Redemption::class);
    }

    public function adjustedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    public function referral(): HasOne
    {
        return $this->hasOne(Referral::class);
    }
}
