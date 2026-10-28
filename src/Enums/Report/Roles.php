<?php

declare(strict_types=1);

namespace Enlivy\Enums\Report;

use Enlivy\Enums\Concern\EnumValues;

enum Roles: string
{
    use EnumValues;

    case AUTHOR = 'author';
    case RECIPIENT = 'recipient';
}
