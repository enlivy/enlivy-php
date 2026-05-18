<?php

declare(strict_types=1);

namespace Enlivy\Enums;

use Enlivy\Enums\Concern\EnumValues;

enum PartyTypes: string
{
    use EnumValues;

    case INDIVIDUAL = 'individual';
    case ORGANIZATION = 'organization';
}
