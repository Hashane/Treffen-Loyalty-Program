<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'outlet_id',
        'check_number',
        'guest_name',
        'department',
        'transaction_date',
        'booking_reference',
        'hotel_property',
        'total_amount',
        'points_earned',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function pointsLedger(): HasMany
    {
        return $this->hasMany(PointsLedger::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(TransactionLineItem::class);
    }
}
