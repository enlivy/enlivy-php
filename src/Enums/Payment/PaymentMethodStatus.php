<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payment;

use Enlivy\Enums\Concern\EnumValues;

enum PaymentMethodStatus: string
{
    use EnumValues;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case EXPIRED = 'expired';
    case REQUIRES_VERIFICATION = 'requires_verification';
    case REMOVED_BY_USER = 'removed_by_user';
    case REMOVED_BY_ADMIN = 'removed_by_admin';
    case REMOVED_BY_PROVIDER = 'removed_by_provider';
}
