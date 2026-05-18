<?php

declare(strict_types=1);

namespace Enlivy\Enums\Webhook;

use Enlivy\Enums\Concern\EnumValues;

enum NotificationStatuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case DROPPED = 'dropped';
    case ANOMALY = 'anomaly';
}
