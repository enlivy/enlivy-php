<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum TaxApplicabilityReasons: string
{
    use EnumValues;

    case SELLER_NOT_REGISTERED = 'seller_not_registered';
    case OUTSIDE_SCOPE = 'outside_scope';
    case DOMESTIC = 'domestic';
    case EU_REVERSE_CHARGE = 'eu_reverse_charge';
    case EU_BUSINESS_WITHOUT_VAT_ID = 'eu_business_without_vat_id';
    case EU_CONSUMER = 'eu_consumer';
}
