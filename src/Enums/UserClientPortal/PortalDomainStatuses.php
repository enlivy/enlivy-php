<?php

declare(strict_types=1);

namespace Enlivy\Enums\UserClientPortal;

use Enlivy\Enums\Concern\EnumValues;

enum PortalDomainStatuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case ACTIVE = 'active';
    case ACTIVE_WITH_SSL = 'active_with_ssl';
    case ERROR = 'error';
}
