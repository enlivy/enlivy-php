<?php

declare(strict_types=1);

namespace Enlivy\Enums\EventDelivery;

use Enlivy\Enums\Concern\EnumValues;

enum DeliveryStatus: string
{
    use EnumValues;

    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case DROPPED = 'dropped';
    case ANOMALY = 'anomaly';
}
