<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum AssuranceModes: string
{
    use EnumValues;

    case FULL_BOOKS = 'full_books';
    case HYBRID = 'hybrid';
    case DECLARED = 'declared';
}
