<?php

namespace App\Models;

use App\Enums\Notifications\Channel;
use App\Enums\Notifications\NotificationType;
use App\Enums\Notifications\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'notification_type',
        'title',
        'message',
        'channel',
        'status',
        'metadata',
        'error_message',
        'scheduled_for',
        'sent_at',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'notification_type' => NotificationType::class,
            'channel' => Channel::class,
            'status' => Status::class,
            'metadata' => 'array',
            'scheduled_for' => 'datetime',
            'sent_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
