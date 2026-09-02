<?php

declare(strict_types=1);

namespace Enlivy\Enums\WorkingTime;

use Enlivy\Enums\Concern\EnumValues;

enum TermUnits: string
{
    use EnumValues;

    case HOURS = 'hours';
    case DAYS = 'days';
}
