<?php

declare(strict_types=1);

namespace Enlivy\Enums\Product;

use Enlivy\Enums\Concern\EnumValues;

enum Types: string
{
    use EnumValues;

    case DIGITAL = 'digital';
    case PHYSICAL = 'physical';
    case SERVICE = 'service';
    case BONUS = 'bonus';
}
