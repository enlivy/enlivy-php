<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum EmailStyles: string
{
    use EnumValues;

    case BRANDED = 'branded';
    case PLAIN = 'plain';
}
