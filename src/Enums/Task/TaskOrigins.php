<?php

declare(strict_types=1);

namespace Enlivy\Enums\Task;

use Enlivy\Enums\Concern\EnumValues;

enum TaskOrigins: string
{
    use EnumValues;

    case MANUAL = 'manual';
    case AUTOMATION = 'automation';
    case IMPORT = 'import';
    case REQUEST = 'request';
}
