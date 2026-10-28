<?php

declare(strict_types=1);

namespace Enlivy\Enums\Apps;

use Enlivy\Enums\Concern\EnumValues;

enum InstallRequestStatuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case APPROVED = 'approved';
    case DECLINED = 'declined';
    case CANCELLED = 'cancelled';
}
