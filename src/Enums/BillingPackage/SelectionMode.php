<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

enum SelectionMode: string
{
    use EnumValues;

    case FIXED = 'fixed';
    case SELECT_ONE = 'select_one';
    case SELECT_MANY = 'select_many';
}
