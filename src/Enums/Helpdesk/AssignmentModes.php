<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum AssignmentModes: string
{
    use EnumValues;

    case MANUAL = 'manual';
    case DEFAULT_TEAMMATE = 'default_teammate';
    case ROUND_ROBIN = 'round_robin';
}
