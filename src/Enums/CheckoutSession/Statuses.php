<?php

declare(strict_types=1);

namespace Enlivy\Enums\CheckoutSession;

use Enlivy\Enums\Concern\EnumValues;

enum Statuses: string
{
    use EnumValues;

    case OPEN = 'open';
    case COMPLETE = 'complete';
    case EXPIRED = 'expired';
}
