<?php

declare(strict_types=1);

namespace Enlivy\Enums\EventTrail;

use Enlivy\Enums\Concern\EnumValues;

enum Origin: string
{
    use EnumValues;

    case BACK_OFFICE = 'back_office';
    case CLIENT_PORTAL = 'client_portal';
    case CRON = 'cron';
    case WEBHOOK = 'webhook';
    case SYSTEM = 'system';
}
