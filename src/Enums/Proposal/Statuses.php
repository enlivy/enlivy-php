<?php

declare(strict_types=1);

namespace Enlivy\Enums\Proposal;

use Enlivy\Enums\Concern\EnumValues;

enum Statuses: string
{
    use EnumValues;

    case DRAFT = 'draft';
    case LEAD_CONFIGURED = 'lead_configured';
    case SENT = 'sent';
    case VIEWED = 'viewed';
    case ACCEPTED = 'accepted';
    case FIRST_PAYMENT_PENDING = 'first_payment_pending';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
}
