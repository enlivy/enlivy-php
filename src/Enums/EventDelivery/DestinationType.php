<?php

declare(strict_types=1);

namespace Enlivy\Enums\EventDelivery;

use Enlivy\Enums\Concern\EnumValues;

enum DestinationType: string
{
    use EnumValues;

    case WEBHOOK = 'webhook';
    case SLACK = 'slack';
    /** Served for an installed App's destination; a destination cannot be created or updated with it. */
    case APP = 'app';
}
