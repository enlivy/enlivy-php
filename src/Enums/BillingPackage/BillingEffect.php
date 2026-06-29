<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

enum BillingEffect: string
{
    use EnumValues;

    case NOW = 'now';
    case NEXT_CYCLE = 'next_cycle';
}
