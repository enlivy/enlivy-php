<?php

declare(strict_types=1);

namespace Enlivy\Enums\BankTransaction;

use Enlivy\Enums\Concern\EnumValues;

enum ConnectionTypes: string
{
    use EnumValues;

    case INVOICE = 'invoice';
    case RECEIPT = 'receipt';
    case BANK_TRANSACTION = 'bank_transaction';
    case USER = 'user';
    case PAYSLIP = 'payslip';
}
