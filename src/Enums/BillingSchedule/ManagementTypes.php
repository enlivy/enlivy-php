<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingSchedule;

use Enlivy\Enums\Concern\EnumValues;

enum ManagementTypes: string
{
    use EnumValues;

    case STRIPE_HOSTED = 'stripe_hosted';
    case APP_MANAGED = 'app_managed';
}
