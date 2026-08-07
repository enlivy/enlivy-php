<?php

declare(strict_types=1);

namespace Enlivy\Enums\Organization;

use Enlivy\Enums\Concern\EnumValues;

enum Environments: string
{
    use EnumValues;

    case LIVE = 'live';
    case SANDBOX = 'sandbox';
}
