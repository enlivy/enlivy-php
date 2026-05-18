<?php

declare(strict_types=1);

namespace Enlivy\Enums\TenantBilling;

use Enlivy\Enums\Concern\EnumValues;

enum BillingCycles: string
{
    use EnumValues;

    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
}
