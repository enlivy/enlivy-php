<?php

declare(strict_types=1);

namespace Enlivy\Enums;

use Enlivy\Enums\Concern\EnumValues;

enum ReusableContentEntityTypes: string
{
    use EnumValues;

    case CONTRACT = 'contract';
    case PLAYBOOK = 'playbook';
}
