<?php

declare(strict_types=1);

namespace Enlivy\Enums\Invoice;

use Enlivy\Enums\Concern\EnumValues;

enum DueDateType: string
{
    use EnumValues;

    case IMMEDIATE = 'immediate';
    case END_OF_MONTH = 'end_of_month';
    case CUSTOM_DAYS = 'custom_days';
}
