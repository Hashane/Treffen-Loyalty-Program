<?php

namespace App\Models;

use App\Enums\Members\IdType;
use App\Enums\Members\PreferredCommunication;
use App\Enums\Members\Status;
use App\Observers\MemberObserver;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

#[ObservedBy([MemberObserver::class])]
class Member extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'member_number',
        'first_name',
        'last_name',
        'qatar_id_or_passport',
        'id_type',
        'date_of_birth',
        'email',
        'phone',
        'preferred_communication',
        'password',
        'email_verified_at',
        'remember_token',
        'failed_login_attempts',
        'locked_until',
        'qr_code_path',
        'qr_code_data',
        'membership_tier_id',
        'current_points',
        'lifetime_points',
        'referral_code',
        'referred_by_member_id',
        'status',
        'enrolled_date',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'id_type' => IdType::class,
            'preferred_communication' => PreferredCommunication::class,
            'status' => Status::class,
            'date_of_birth' => 'date',
            'email_verified_at' => 'datetime',
            'locked_until' => 'datetime',
            'enrolled_date' => 'datetime',
        ];
    }

    public function membershipTier(): BelongsTo
    {
        return $this->belongsTo(MembershipTier::class);
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'referred_by_member_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function pointsLedger(): HasMany
    {
        return $this->hasMany(PointsLedger::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }

    public function tierHistory(): HasMany
    {
        return $this->hasMany(TierHistory::class);
    }

    public function referralsMade(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_member_id');
    }

    public function referralsReceived(): HasMany
    {
        return $this->hasMany(Referral::class, 'referred_member_id');
    }

    public function oauthConnections(): HasMany
    {
        return $this->hasMany(OauthConnection::class);
    }

    public function verificationCodes(): HasMany
    {
        return $this->hasMany(VerificationCode::class);
    }

    public function getAvailablePointsAttribute()
    {
        return $this->pointsLedgers()
            ->where('expired', false)
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', now());
            })
            ->sum('points_change');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getNextTierAttribute(): ?MembershipTier
    {
        if (!$this->membershipTier) {
            return null;
        }

        // Cache within the request lifecycle to avoid duplicate queries
        return once(function () {
            return MembershipTier::where('tier_level', '>', $this->membershipTier->tier_level)
                ->orderBy('tier_level')
                ->first();
        });
    }

    public function getNextTierPointsAttribute(): ?int
    {
        return $this->next_tier?->points_threshold;
    }

    public function getPointsToNextTierAttribute(): ?int
    {
        if (!$this->next_tier) {
            return null;
        }

        return max(0, $this->next_tier->points_threshold - $this->lifetime_points);
    }

    public function getInitialsAttribute(): string
    {
        return Str::of($this->full_name)
            ->explode(' ')
            ->map(fn($part) => Str::of($part)->substr(0, 1)->upper())
            ->join('');
    }

    public function getExpiringPointsAttribute()
    {
        return $this->pointsLedgers()
            ->where('expired', false)
            ->where('points_change', '>', 0)
            ->whereBetween('expiry_date', [now(), now()->addDays(30)])
            ->sum('points_change');
    }
}
