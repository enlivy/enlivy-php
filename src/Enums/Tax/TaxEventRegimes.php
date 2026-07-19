<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum TaxEventRegimes: string
{
    use EnumValues;

    case ACCRUAL = 'accrual';
    case CASH = 'cash';
}
