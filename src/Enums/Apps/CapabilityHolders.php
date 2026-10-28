<?php

declare(strict_types=1);

namespace Enlivy\Enums\Apps;

use Enlivy\Enums\Concern\EnumValues;

enum CapabilityHolders: string
{
    use EnumValues;

    case EVERYONE = 'everyone';
    case ADMINS = 'admins';
    case ROLES = 'roles';
}
