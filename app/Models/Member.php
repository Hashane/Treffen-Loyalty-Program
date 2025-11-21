<?php

namespace App\Models;

use App\Enums\Members\IdType;
use App\Enums\Members\PreferredCommunication;
use App\Enums\Members\Status;
use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory;

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
        'email_verified',
        'email_verified_at',
        'email_verification_token',
        'failed_login_attempts',
        'locked_until',
        'password_reset_token',
        'password_reset_expires',
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
        'password_hash',
        'email_verification_token',
        'password_reset_token',
    ];

    protected function casts(): array
    {
        return [
            'id_type' => IdType::class,
            'preferred_communication' => PreferredCommunication::class,
            'status' => Status::class,
            'date_of_birth' => 'date',
            'email_verified' => 'boolean',
            'email_verified_at' => 'datetime',
            'locked_until' => 'datetime',
            'password_reset_expires' => 'datetime',
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
}
