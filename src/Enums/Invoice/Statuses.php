<?php

declare(strict_types=1);

namespace Enlivy\Enums\Invoice;

use Enlivy\Enums\Concern\EnumValues;

enum Statuses: string
{
    use EnumValues;

    case APPROVAL_REQUIRED = 'approval_required';
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case PENDING = 'pending';
    case SENT_EMAIL = 'sent_email';
    case SENT_PHYSICAL = 'sent_physical';
    case PAYMENT_PARTIAL = 'payment_partial';
    case PAID = 'paid';
    case SOLVED = 'solved';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';
}
