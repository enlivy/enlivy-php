<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum EmailCollectModes: string
{
    use EnumValues;

    case NEVER = 'never';
    case OUTSIDE_HOURS = 'outside_hours';
    case ALWAYS = 'always';
}
