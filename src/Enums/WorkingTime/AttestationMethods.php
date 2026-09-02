<?php

declare(strict_types=1);

namespace Enlivy\Enums\WorkingTime;

use Enlivy\Enums\Concern\EnumValues;

enum AttestationMethods: string
{
    use EnumValues;

    case WORKER_CONFIRMED = 'worker_confirmed';
    case SUPERVISOR_APPROVED = 'supervisor_approved';
    case PERIOD_CLOSE = 'period_close';
    case SIGNED_DOCUMENT = 'signed_document';
}
