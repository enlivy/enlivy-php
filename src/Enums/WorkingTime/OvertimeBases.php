<?php

declare(strict_types=1);

namespace Enlivy\Enums\WorkingTime;

use Enlivy\Enums\Concern\EnumValues;

enum OvertimeBases: string
{
    use EnumValues;

    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case DAILY_AND_WEEKLY = 'daily_and_weekly';
}
