<?php

declare(strict_types=1);

namespace Enlivy\Enums\Contract;

use Enlivy\Enums\Concern\EnumValues;

enum SignatureStatuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case SENT = 'sent';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';
    case VOID = 'void';
}
