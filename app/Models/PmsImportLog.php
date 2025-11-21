<?php

namespace App\Models;

use App\Enums\PmsImportLogs\ImportType;
use App\Enums\PmsImportLogs\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PmsImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_type',
        'file_name',
        'file_size_kb',
        'records_processed',
        'records_successful',
        'records_failed',
        'records_duplicate',
        'error_details',
        'summary',
        'status',
        'imported_by',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'import_type' => ImportType::class,
            'status' => Status::class,
            'error_details' => 'array',
            'summary' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
