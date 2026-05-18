<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payslip;

use Enlivy\Enums\Concern\EnumValues;

enum Fields: string
{
    use EnumValues;

    case TEXT = 'text';
    case NUMBER = 'number';
    case NUMBER_CURRENCY = 'number_currency';
    case NUMBER_PERCENTAGE = 'number_percentage';
}
