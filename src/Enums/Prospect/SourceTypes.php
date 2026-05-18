<?php

declare(strict_types=1);

namespace Enlivy\Enums\Prospect;

use Enlivy\Enums\Concern\EnumValues;

enum SourceTypes: string
{
    use EnumValues;

    case INBOUND = 'inbound';
    case OUTBOUND = 'outbound';
}
