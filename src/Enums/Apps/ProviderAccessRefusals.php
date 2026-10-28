<?php

declare(strict_types=1);

namespace Enlivy\Enums\Apps;

use Enlivy\Enums\Concern\EnumValues;

enum ProviderAccessRefusals: string
{
    use EnumValues;

    case CONNECTION_DISCONNECTED = 'connection_disconnected';
    case CONNECTION_REVOKED = 'connection_revoked';
    case INSTALLATION_SUSPENDED = 'installation_suspended';
    case INSTALLATION_REMOVED = 'installation_removed';
    case APP_UNAVAILABLE = 'app_unavailable';
    case PERMISSION_MISSING = 'permission_missing';
    case FORBIDDEN = 'forbidden';
}
