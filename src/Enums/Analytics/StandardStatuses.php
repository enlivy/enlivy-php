<?php

declare(strict_types=1);

namespace Enlivy\Enums\Analytics;

use Enlivy\Enums\Concern\EnumValues;

enum StandardStatuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case SCHEDULED = 'scheduled';
}
