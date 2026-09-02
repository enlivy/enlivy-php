<?php

declare(strict_types=1);

namespace Enlivy\Enums\Employment;

use Enlivy\Enums\Concern\EnumValues;

enum JurisdictionAxes: string
{
    use EnumValues;

    case WORK = 'work';
    case PAYROLL = 'payroll';
    case SOCIAL_SECURITY = 'social_security';
    case TAX = 'tax';
}
