<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

/** Only SALE issues fiscal documents; FUNDING and AGREEMENT never produce an invoice. */
enum OutcomeMode: string
{
    use EnumValues;

    case SALE = 'sale';
    case FUNDING = 'funding';
    case AGREEMENT = 'agreement';
}
