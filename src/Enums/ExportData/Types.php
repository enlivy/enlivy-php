<?php

declare(strict_types=1);

namespace Enlivy\Enums\ExportData;

use Enlivy\Enums\Concern\EnumValues;

enum Types: string
{
    use EnumValues;

    case FULL = 'full';
    case ACCOUNTING_SAGA = 'accounting_saga';
}
