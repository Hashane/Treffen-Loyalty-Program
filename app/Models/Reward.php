<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'points_required',
        'qar_value',
        'category_id',
        'tier_requirement_id',
        'available_quantity',
        'is_unlimited',
        'image_url',
        'terms_conditions',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_unlimited' => 'boolean',
            'is_active' => 'boolean',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RewardCategory::class);
    }

    public function tierRequirement(): BelongsTo
    {
        return $this->belongsTo(MembershipTier::class, 'tier_requirement_id');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }
}
