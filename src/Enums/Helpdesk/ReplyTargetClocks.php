<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum ReplyTargetClocks: string
{
    use EnumValues;

    case CALENDAR = 'calendar';
    case WORKING_HOURS = 'working_hours';
}
