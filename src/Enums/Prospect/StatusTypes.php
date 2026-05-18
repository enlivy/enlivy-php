<?php

declare(strict_types=1);

namespace Enlivy\Enums\Prospect;

use Enlivy\Enums\Concern\EnumValues;

enum StatusTypes: string
{
    use EnumValues;

    case OPEN = 'open';
    case QUALIFIED = 'qualified';
    case DISQUALIFIED = 'disqualified';
    case WON = 'won';
    case LOST = 'lost';
}
