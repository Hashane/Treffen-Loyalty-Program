<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OauthConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'provider',
        'provider_id',
        'provider_token',
        'provider_refresh_token',
        'avatar',
    ];

    protected function casts(): array
    {
        return [
            'provider_token' => 'encrypted',
            'provider_refresh_token' => 'encrypted',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
