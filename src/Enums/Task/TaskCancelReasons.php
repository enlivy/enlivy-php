<?php

declare(strict_types=1);

namespace Enlivy\Enums\Task;

use Enlivy\Enums\Concern\EnumValues;

enum TaskCancelReasons: string
{
    use EnumValues;

    case NO_LONGER_NEEDED = 'no_longer_needed';
    case DUPLICATE = 'duplicate';
    case PARENT_CANCELLED = 'parent_cancelled';
}
