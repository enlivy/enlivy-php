<?php

declare(strict_types=1);

namespace Enlivy\Enums\Apps;

use Enlivy\Enums\Concern\EnumValues;

enum WidgetStatuses: string
{
    use EnumValues;

    case READY = 'ready';
    case DISCONNECTED = 'disconnected';
    case SUSPENDED = 'suspended';
    case UNAVAILABLE = 'unavailable';
}
