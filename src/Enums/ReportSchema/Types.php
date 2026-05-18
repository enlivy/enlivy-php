<?php

declare(strict_types=1);

namespace Enlivy\Enums\ReportSchema;

use Enlivy\Enums\Concern\EnumValues;

enum Types: string
{
    use EnumValues;

    case STANDARD = 'standard';
    case STANDUP = 'standup';
    case END_OF_DAY = 'end_of_day';
}
