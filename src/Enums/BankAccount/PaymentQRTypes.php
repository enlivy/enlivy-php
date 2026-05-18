<?php

declare(strict_types=1);

namespace Enlivy\Enums\BankAccount;

use Enlivy\Enums\Concern\EnumValues;

enum PaymentQRTypes: string
{
    use EnumValues;

    case ISO_20022 = 'iso_20022';
    case ROPAY = 'ropay';
    case SEPA = 'sepa';
}
