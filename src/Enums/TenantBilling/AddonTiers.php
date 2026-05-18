<?php

declare(strict_types=1);

namespace Enlivy\Enums\TenantBilling;

use Enlivy\Enums\Concern\EnumValues;

enum AddonTiers: string
{
    use EnumValues;

    case NONE = 'none';
    case TIER_1 = 'tier_1';
    case TIER_2 = 'tier_2';
    case TIER_3 = 'tier_3';
}
