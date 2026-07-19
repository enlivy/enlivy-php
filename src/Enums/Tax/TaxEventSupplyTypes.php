<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum TaxEventSupplyTypes: string
{
    use EnumValues;

    case GOODS = 'goods';
    case SERVICES = 'services';
    case TRIANGULAR = 'triangular';
}
