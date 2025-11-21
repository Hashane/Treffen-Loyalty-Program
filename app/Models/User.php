<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'name',
        'outlet_id',
        'is_active',
        'last_login',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login' => 'datetime',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class, 'staff_user_id');
    }

    public function pmsImportLogs(): HasMany
    {
        return $this->hasMany(PmsImportLog::class, 'imported_by');
    }

    public function pointsAdjustments(): HasMany
    {
        return $this->hasMany(PointsLedger::class, 'adjusted_by');
    }
}
