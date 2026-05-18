<?php

declare(strict_types=1);

namespace Enlivy\Enums\BankAccount;

use Enlivy\Enums\Concern\EnumValues;

enum Types: string
{
    use EnumValues;

    case STANDARD = 'standard';
    case PAYPAL = 'paypal';
    case STRIPE = 'stripe';
}
