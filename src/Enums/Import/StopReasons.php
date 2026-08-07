<?php

declare(strict_types=1);

namespace Enlivy\Enums\Import;

use Enlivy\Enums\Concern\EnumValues;

enum StopReasons: string
{
    use EnumValues;

    case USAGE_LIMIT = 'usage_limit';
    case AI_LIMIT = 'ai_limit';
    case CONSECUTIVE_FAILURES = 'consecutive_failures';
    case FILE_UNREADABLE = 'file_unreadable';
}
