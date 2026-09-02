<?php

declare(strict_types=1);

namespace Enlivy\Enums\WorkingTime;

use Enlivy\Enums\Concern\EnumValues;

enum DayDispositions: string
{
    use EnumValues;

    case WORKED = 'worked';
    case ABSENT = 'absent';
    case REST_DAY = 'rest_day';
    case PUBLIC_HOLIDAY = 'public_holiday';
    case SCHEDULED_NON_WORKING_DAY = 'scheduled_non_working_day';
    case UNKNOWN = 'unknown';
}
