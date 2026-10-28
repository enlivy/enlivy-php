<?php

declare(strict_types=1);

namespace Enlivy\Enums\Apps;

use Enlivy\Enums\Concern\EnumValues;

enum Distributions: string
{
    use EnumValues;

    case PUBLIC = 'public';
    case PERSONAL = 'personal';
}
