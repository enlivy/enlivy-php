<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingSchedule;

use Enlivy\Enums\Concern\EnumValues;

enum ScheduledPaymentStatuses: string
{
    use EnumValues;

    case DRAFT = 'draft';
    case AWAITING_PAYMENT = 'awaiting_payment';
    case OVERDUE = 'overdue';
    case PAID = 'paid';
    case DISMISSED = 'dismissed';
}
