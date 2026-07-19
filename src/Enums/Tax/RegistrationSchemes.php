<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum RegistrationSchemes: string
{
    use EnumValues;

    case VAT_REGISTERED = 'vat_registered';
    case SMALL_BUSINESS_DOMESTIC = 'small_business_domestic';
    case SMALL_BUSINESS_CROSS_BORDER = 'small_business_cross_border';
    case OSS_UNION = 'oss_union';
    case OSS_NON_UNION = 'oss_non_union';
    case IOSS = 'ioss';
    case NOT_REGISTERED = 'not_registered';
}
