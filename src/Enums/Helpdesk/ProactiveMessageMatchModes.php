<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum ProactiveMessageMatchModes: string
{
    use EnumValues;

    case EXACT = 'exact';
    case CONTAINS = 'contains';
    case STARTS_WITH = 'starts_with';
    case REGEX = 'regex';
}
