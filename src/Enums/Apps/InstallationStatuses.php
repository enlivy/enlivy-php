<?php

declare(strict_types=1);

namespace Enlivy\Enums\Apps;

use Enlivy\Enums\Concern\EnumValues;

enum InstallationStatuses: string
{
    use EnumValues;

    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case REMOVED = 'removed';
}
