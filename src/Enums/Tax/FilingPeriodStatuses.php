<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum FilingPeriodStatuses: string
{
    use EnumValues;

    case OPEN = 'open';
    case CLOSED = 'closed';
    case FILED = 'filed';
    case SUBMITTED = 'submitted';
}
