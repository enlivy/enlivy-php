<?php

declare(strict_types=1);

namespace Enlivy\Enums\TenantBilling;

use Enlivy\Enums\Concern\EnumValues;

enum FeaturePackCategories: string
{
    use EnumValues;

    case FINANCIAL = 'financial';
    case PRODUCTIVITY = 'productivity';
    case INSIGHTS = 'insights';
    case BRAND = 'brand';
    case OTHER = 'other';
}
