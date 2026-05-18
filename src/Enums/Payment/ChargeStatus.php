<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payment;

use Enlivy\Enums\Concern\EnumValues;

enum ChargeStatus: string
{
    use EnumValues;

    case IN_PROGRESS = 'in_progress';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
    case REQUIRES_ACTION = 'requires_action';
    case ALREADY_PAID = 'already_paid';
}
