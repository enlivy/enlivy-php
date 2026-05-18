<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

enum ProrationPolicy: string
{
    use EnumValues;

    case NONE = 'none';
    case PRORATE_IMMEDIATELY = 'prorate_immediately';
    case PRORATE_NEXT_INVOICE = 'prorate_next_invoice';
}
