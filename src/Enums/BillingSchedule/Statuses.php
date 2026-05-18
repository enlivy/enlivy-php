<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingSchedule;

use Enlivy\Enums\Concern\EnumValues;

enum Statuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
