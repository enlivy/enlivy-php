<?php

declare(strict_types=1);

namespace Enlivy\Enums\ReportSchema;

use Enlivy\Enums\Concern\EnumValues;

enum Frequencies: string
{
    use EnumValues;

    case ON_DEMAND = 'on_demand';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case QUARTERLY = 'quarterly';
}
