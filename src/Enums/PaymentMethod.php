<?php

declare(strict_types=1);

namespace Enlivy\Enums;

use Enlivy\Enums\Concern\EnumValues;

enum PaymentMethod: string
{
    use EnumValues;

    case BANK_TRANSFER = 'bank_transfer';
    case CARD_PAYMENT = 'card_payment';
    case STRIPE_CARD_PAYMENT = 'stripe_card_payment';
    case PAYPAL = 'paypal';
    case CASH = 'cash';
    case PAID_BY_PERSONAL_FUNDS = 'paid_by_personal_funds';
}
