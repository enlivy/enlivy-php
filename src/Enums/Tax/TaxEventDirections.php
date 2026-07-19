<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum TaxEventDirections: string
{
    use EnumValues;

    case OUTPUT = 'output';
    case INPUT = 'input';
}
