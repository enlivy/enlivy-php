<?php

declare(strict_types=1);

namespace Enlivy\Enums\Employment;

use Enlivy\Enums\Concern\EnumValues;

enum RegistryStatuses: string
{
    use EnumValues;

    case UNKNOWN = 'unknown';
    case NOT_REQUIRED = 'not_required';
    case REQUIRED = 'required';
    case CONFIRMED = 'confirmed';
}
