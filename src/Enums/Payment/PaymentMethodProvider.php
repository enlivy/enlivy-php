<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payment;

use Enlivy\Enums\Concern\EnumValues;

enum PaymentMethodProvider: string
{
    use EnumValues;

    case STRIPE = 'stripe';
    case PAYPAL = 'paypal';
}
