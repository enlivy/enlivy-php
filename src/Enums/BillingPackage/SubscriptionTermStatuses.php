<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

enum SubscriptionTermStatuses: string
{
    use EnumValues;

    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
}
