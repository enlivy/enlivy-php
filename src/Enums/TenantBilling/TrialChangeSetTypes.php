<?php

declare(strict_types=1);

namespace Enlivy\Enums\TenantBilling;

use Enlivy\Enums\Concern\EnumValues;

enum TrialChangeSetTypes: string
{
    use EnumValues;

    case ADD = 'add';
    case DROP = 'drop';
}
