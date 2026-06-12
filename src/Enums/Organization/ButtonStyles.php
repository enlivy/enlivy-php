<?php

declare(strict_types=1);

namespace Enlivy\Enums\Organization;

use Enlivy\Enums\Concern\EnumValues;

enum ButtonStyles: string
{
    use EnumValues;

    case PRIMARY = 'primary';
    case SECONDARY = 'secondary';
    case OUTLINE = 'outline';
}
