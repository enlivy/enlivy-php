<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payslip;

use Enlivy\Enums\Concern\EnumValues;

enum LineSources: string
{
    use EnumValues;

    case ENTERED = 'entered';
    case COMPUTED = 'computed';
    case IMPORTED = 'imported';
}
