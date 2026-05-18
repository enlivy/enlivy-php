<?php

declare(strict_types=1);

namespace Enlivy\Enums;

use Enlivy\Enums\Concern\EnumValues;

enum ExportDataStatuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case COOLDOWN = 'cooldown';
    case PROGRESSING = 'progressing';
    case COMPLETED = 'completed';
    case ERROR = 'error';
}
