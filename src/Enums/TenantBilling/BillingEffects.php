<?php

declare(strict_types=1);

namespace Enlivy\Enums\TenantBilling;

use Enlivy\Enums\Concern\EnumValues;

enum BillingEffects: string
{
    use EnumValues;

    case PRORATED_NOW = 'prorated_now';
    case TRIAL = 'trial';
    case NEXT_CYCLE = 'next_cycle';
    case NONE = 'none';
}
