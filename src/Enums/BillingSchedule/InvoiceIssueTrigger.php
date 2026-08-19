<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingSchedule;

use Enlivy\Enums\Concern\EnumValues;

enum InvoiceIssueTrigger: string
{
    use EnumValues;

    case ON_GENERATION = 'on_generation';
    case ON_PAYMENT = 'on_payment';
}
