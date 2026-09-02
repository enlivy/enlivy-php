<?php

declare(strict_types=1);

namespace Enlivy\Enums\WorkingTime;

use Enlivy\Enums\Concern\EnumValues;

enum LimitBreaches: string
{
    use EnumValues;

    case DAILY_REST = 'daily_rest';
    case WEEKLY_REST = 'weekly_rest';
    case WEEKLY_AVERAGE = 'weekly_average';
    case WEEKLY_ABSOLUTE = 'weekly_absolute';
    case NIGHT_AVERAGE = 'night_average';
    case DAILY_MAXIMUM = 'daily_maximum';
}
