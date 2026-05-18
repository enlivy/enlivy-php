<?php

declare(strict_types=1);

namespace Enlivy\Enums\TenantBilling;

use Enlivy\Enums\Concern\EnumValues;

enum FeaturePackTiers: string
{
    use EnumValues;

    case NONE = 'none';
    case STANDARD = 'standard';
    case UNLIMITED = 'unlimited';
}
