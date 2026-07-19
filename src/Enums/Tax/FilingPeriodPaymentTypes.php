<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum FilingPeriodPaymentTypes: string
{
    use EnumValues;

    case PAYMENT = 'payment';
    case REFUND = 'refund';
    case ADVANCE = 'advance';
    case PENALTY = 'penalty';
    case INTEREST = 'interest';
}
