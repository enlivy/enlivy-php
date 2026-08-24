<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

enum ExchangeRateGuarantees: string
{
    use EnumValues;

    case INVOICE = 'invoice';
    case ACCEPTANCE = 'acceptance';
}
