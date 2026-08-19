<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

enum TierPriceType: string
{
    use EnumValues;

    case FIXED = 'fixed';
    case PERCENT_OF_BASELINE = 'percent_of_baseline';
}
