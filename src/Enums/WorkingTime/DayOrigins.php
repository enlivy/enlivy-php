<?php

declare(strict_types=1);

namespace Enlivy\Enums\WorkingTime;

use Enlivy\Enums\Concern\EnumValues;

enum DayOrigins: string
{
    use EnumValues;

    case DERIVED = 'derived';
    case AUTHORED = 'authored';
    case IMPORTED = 'imported';
    case CORRECTED = 'corrected';
}
