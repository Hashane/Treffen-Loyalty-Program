<?php

namespace App\Enums\PmsImportLogs;

enum ImportType: string
{
    case API = 'API';
    case CSV = 'CSV';
    case MANUAL = 'MANUAL';
}
