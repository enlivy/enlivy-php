<?php

declare(strict_types=1);

namespace Enlivy\Enums\WorkingTime;

use Enlivy\Enums\Concern\EnumValues;

enum RetentionStatuses: string
{
    use EnumValues;

    case PROTECTED = 'protected';
    case RETAINED = 'retained';
    case EXPIRED = 'expired';
    case UNRESOLVED = 'unresolved';
}
