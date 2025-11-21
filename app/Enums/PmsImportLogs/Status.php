<?php

namespace App\Enums\PmsImportLogs;

enum Status: string
{
    case PROCESSING = 'PROCESSING';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';
    case PARTIAL = 'PARTIAL';
}
