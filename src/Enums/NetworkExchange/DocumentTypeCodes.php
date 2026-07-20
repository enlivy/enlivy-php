<?php

declare(strict_types=1);

namespace Enlivy\Enums\NetworkExchange;

use Enlivy\Enums\Concern\EnumValues;

enum DocumentTypeCodes: string
{
    use EnumValues;

    case COMMERCIAL_INVOICE = '380';
    case CREDIT_NOTE = '381';
}
