<?php

declare(strict_types=1);

namespace Enlivy\Enums\WorkingTime;

use Enlivy\Enums\Concern\EnumValues;

enum RecordSourceTypes: string
{
    use EnumValues;

    case DOCUMENT = 'document';
    case IMPORT_BATCH = 'import_batch';
    case DEVICE_EVENT = 'device_event';
    case EXTERNAL_SYSTEM = 'external_system';
}
