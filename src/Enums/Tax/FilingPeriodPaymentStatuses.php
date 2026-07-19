<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum FilingPeriodPaymentStatuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case CLEARED = 'cleared';
    case FAILED = 'failed';
}
