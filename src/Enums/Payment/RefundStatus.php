<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payment;

use Enlivy\Enums\Concern\EnumValues;

enum RefundStatus: string
{
    use EnumValues;

    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
    case PENDING = 'pending';
}
