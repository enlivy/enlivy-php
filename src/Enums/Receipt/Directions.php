<?php

declare(strict_types=1);

namespace Enlivy\Enums\Receipt;

use Enlivy\Enums\Concern\EnumValues;

enum Directions: string
{
    use EnumValues;

    case INBOUND = 'inbound';
    case OUTBOUND = 'outbound';
}
