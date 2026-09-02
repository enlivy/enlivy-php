<?php

declare(strict_types=1);

namespace Enlivy\Enums\Employment;

use Enlivy\Enums\Concern\EnumValues;

enum FactSources: string
{
    use EnumValues;

    case CONTRACT = 'contract';
    case OFFICIAL_REGISTRY = 'official_registry';
    case USER_ASSERTED = 'user_asserted';
    case MIGRATED_INFERRED = 'migrated_inferred';
    case UNKNOWN = 'unknown';
}
