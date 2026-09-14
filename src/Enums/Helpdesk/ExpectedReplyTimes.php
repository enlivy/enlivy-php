<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum ExpectedReplyTimes: string
{
    use EnumValues;

    case MINUTES = 'minutes';
    case HOURS = 'hours';
    case DAY = 'day';
}
