<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum TaxFamilies: string
{
    use EnumValues;

    case VAT = 'vat';
    case SALES_TAX = 'sales_tax';
    case INCOME_TAX = 'income_tax';
    case PAYROLL = 'payroll';
}
