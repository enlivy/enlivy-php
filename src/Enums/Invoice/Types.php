<?php

declare(strict_types=1);

namespace Enlivy\Enums\Invoice;

use Enlivy\Enums\Concern\EnumValues;

enum Types: string
{
    use EnumValues;

    case STANDARD = 'standard';
    case REVERSAL = 'reversal';
    case PROFORMA = 'proforma';
}
