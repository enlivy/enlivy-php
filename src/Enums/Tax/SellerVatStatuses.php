<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum SellerVatStatuses: string
{
    use EnumValues;

    case UNDECLARED = 'undeclared';
    case REGISTERED = 'registered';
    case SMALL_BUSINESS_EXEMPT = 'small_business_exempt';
    case NOT_REGISTERED = 'not_registered';
    case NOT_APPLICABLE = 'not_applicable';
}
