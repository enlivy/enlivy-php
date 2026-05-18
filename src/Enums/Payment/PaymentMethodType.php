<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payment;

use Enlivy\Enums\Concern\EnumValues;

enum PaymentMethodType: string
{
    use EnumValues;

    case CARD = 'card';
    case APPLE_PAY = 'apple_pay';
    case GOOGLE_PAY = 'google_pay';
    case PAYPAL = 'paypal';
    case SEPA_DEBIT = 'sepa_debit';
    case US_BANK_ACCOUNT = 'us_bank_account';
    case BACS_DEBIT = 'bacs_debit';
    case ACH_DEBIT = 'ach_debit';
    case KLARNA = 'klarna';
    case IDEAL = 'ideal';
}
