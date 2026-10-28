<?php

declare(strict_types=1);

namespace Enlivy\Enums\Apps;

use Enlivy\Enums\Concern\EnumValues;

enum ConnectionStatuses: string
{
    use EnumValues;

    case CONNECTED = 'connected';
    case DISCONNECTED = 'disconnected';
    case REVOKED = 'revoked';
}
