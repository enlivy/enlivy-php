<?php

declare(strict_types=1);

namespace Enlivy\Enums\WorkingTime;

use Enlivy\Enums\Concern\EnumValues;

enum AttestationStatuses: string
{
    use EnumValues;

    case UNATTESTED = 'unattested';
    case ATTESTED = 'attested';
    case VOIDED = 'voided';
}
