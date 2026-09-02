<?php

declare(strict_types=1);

namespace Enlivy\Enums\Employment;

use Enlivy\Enums\Concern\EnumValues;

enum FilingCadences: string
{
    use EnumValues;

    case MONTHLY = 'monthly';
    case PER_PAYDAY = 'per_payday';
    case QUARTERLY = 'quarterly';
    case ANNUAL = 'annual';
    case EVENT_DRIVEN = 'event_driven';
}
