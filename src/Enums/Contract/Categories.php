<?php

declare(strict_types=1);

namespace Enlivy\Enums\Contract;

use Enlivy\Enums\Concern\EnumValues;

enum Categories: string
{
    use EnumValues;

    case CORE = 'core';
    case AMENDMENT = 'amendment';
    case ADDENDA = 'addenda';
    case SUPPLEMENT = 'supplement';
}
