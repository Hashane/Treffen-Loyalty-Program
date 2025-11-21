<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TierHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'from_tier',
        'to_tier',
        'lifetime_points_at_upgrade',
        'current_points_at_upgrade',
        'upgraded_at',
    ];

    protected function casts(): array
    {
        return [
            'upgraded_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
