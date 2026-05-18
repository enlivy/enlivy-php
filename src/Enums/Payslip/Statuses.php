<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payslip;

use Enlivy\Enums\Concern\EnumValues;

enum Statuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case APPROVAL_REQUIRED = 'approval_required';
    case REJECTED = 'rejected';
    case APPROVED = 'approved';
    case PAID = 'paid';
}
