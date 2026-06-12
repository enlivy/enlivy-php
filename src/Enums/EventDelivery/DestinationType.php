<?php

declare(strict_types=1);

namespace Enlivy\Enums\EventDelivery;

use Enlivy\Enums\Concern\EnumValues;

enum DestinationType: string
{
    use EnumValues;

    case WEBHOOK = 'webhook';
    case SLACK = 'slack';
}
