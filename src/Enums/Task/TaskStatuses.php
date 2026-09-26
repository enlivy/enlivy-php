<?php

declare(strict_types=1);

namespace Enlivy\Enums\Task;

use Enlivy\Enums\Concern\EnumValues;

enum TaskStatuses: string
{
    use EnumValues;

    case NOT_STARTED = 'not_started';
    case IN_PROGRESS = 'in_progress';
    case WAITING = 'waiting';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
