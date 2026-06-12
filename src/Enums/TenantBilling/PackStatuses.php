<?php

declare(strict_types=1);

namespace Enlivy\Enums\TenantBilling;

use Enlivy\Enums\Concern\EnumValues;

enum PackStatuses: string
{
    use EnumValues;

    case INACTIVE = 'inactive';
    case TRIALING = 'trialing';
    case TRIALING_CANCELLED = 'trialing_cancelled';
    case ACTIVE = 'active';
    case PAST_DUE = 'past_due';
    case CANCELLED = 'cancelled';
    case LIFETIME = 'lifetime';
}
