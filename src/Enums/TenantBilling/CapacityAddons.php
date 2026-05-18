<?php

declare(strict_types=1);

namespace Enlivy\Enums\TenantBilling;

use Enlivy\Enums\Concern\EnumValues;

enum CapacityAddons: string
{
    use EnumValues;

    case AI = 'ai';
    case STORAGE = 'storage';
    case TRANSLATIONS = 'translations';
    case BANK_CONNECTIONS = 'bank_connections';
}
