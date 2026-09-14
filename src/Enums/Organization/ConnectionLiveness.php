<?php

declare(strict_types=1);

namespace Enlivy\Enums\Organization;

use Enlivy\Enums\Concern\EnumValues;

enum ConnectionLiveness: string
{
    use EnumValues;

    case LIVE = 'live';
    case HISTORICAL = 'historical';
    case TRASHED = 'trashed';
}
