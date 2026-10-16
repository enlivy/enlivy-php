<?php

declare(strict_types=1);

namespace Enlivy\Enums\ReportSchema;

use Enlivy\Enums\Concern\EnumValues;

enum Requirements: string
{
    use EnumValues;

    case NONE = 'none';
    case OPTIONAL = 'optional';
    case REQUIRED = 'required';
}
