<?php

declare(strict_types=1);

namespace Enlivy\Enums\BlockedIdentifier;

use Enlivy\Enums\Concern\EnumValues;

/**
 * `ALL` is a filter directive accepted by the list endpoint, not a value a
 * stored row ever carries — a row is always `ORGANIZATION` or `PLATFORM`.
 */
enum Sources: string
{
    use EnumValues;

    case ORGANIZATION = 'organization';
    case PLATFORM = 'platform';
    case ALL = 'all';
}
