<?php

declare(strict_types=1);

namespace Enlivy\Enums\CheckoutSession;

use Enlivy\Enums\Concern\EnumValues;

enum PaymentStatuses: string
{
    use EnumValues;

    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case NO_PAYMENT_REQUIRED = 'no_payment_required';
}
