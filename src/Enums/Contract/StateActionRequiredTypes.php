<?php

declare(strict_types=1);

namespace Enlivy\Enums\Contract;

use Enlivy\Enums\Concern\EnumValues;

enum StateActionRequiredTypes: string
{
    use EnumValues;

    case PARTIES_SIGNATURES_REQUIRED = 'parties_signatures_required';
}
