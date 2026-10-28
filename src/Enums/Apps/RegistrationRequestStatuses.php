<?php

declare(strict_types=1);

namespace Enlivy\Enums\Apps;

use Enlivy\Enums\Concern\EnumValues;

enum RegistrationRequestStatuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case APPROVED = 'approved';
    case CLAIMED = 'claimed';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
}
