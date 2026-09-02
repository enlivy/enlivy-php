<?php

declare(strict_types=1);

namespace Enlivy\Enums\Employment;

use Enlivy\Enums\Concern\EnumValues;

enum PayPeriods: string
{
    use EnumValues;

    case WEEKLY = 'weekly';
    case BIWEEKLY = 'biweekly';
    case SEMI_MONTHLY = 'semi_monthly';
    case FOUR_WEEKLY = 'four_weekly';
    case MONTHLY = 'monthly';
}
