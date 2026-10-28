<?php

declare(strict_types=1);

namespace Enlivy\Enums\Apps;

use Enlivy\Enums\Concern\EnumValues;

enum LifecycleEvents: string
{
    use EnumValues;

    case INSTALLED = 'app.installed';
    case UPGRADED = 'app.upgraded';
    case PERMISSIONS_CHANGED = 'app.permissions_changed';
    case SUSPENDED = 'app.suspended';
    case RESUMED = 'app.resumed';
    case REMOVED = 'app.removed';
    case SWITCHED_OFF = 'app.switched_off';
    case SWITCHED_ON = 'app.switched_on';
    case CONNECTION_REVOKED = 'connection.revoked';
}
