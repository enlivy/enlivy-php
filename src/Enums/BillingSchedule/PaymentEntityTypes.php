<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingSchedule;

use Enlivy\Enums\Concern\EnumValues;

enum PaymentEntityTypes: string
{
    use EnumValues;

    case INVOICE = 'invoice';
    case RECEIPT = 'receipt';
    case PAYSLIP = 'payslip';
}
