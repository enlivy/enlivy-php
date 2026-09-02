<?php

declare(strict_types=1);

namespace Enlivy\Enums\Prospect;

use Enlivy\Enums\Concern\EnumValues;

enum DuplicateConfidence: string
{
    use EnumValues;

    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';
}
