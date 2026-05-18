<?php

declare(strict_types=1);

namespace Enlivy\Enums\Invoice;

use Enlivy\Enums\Concern\EnumValues;

enum DeliveryMethods: string
{
    use EnumValues;

    case EMAIL = 'email';
    case DELEGATE = 'delegate';
}
