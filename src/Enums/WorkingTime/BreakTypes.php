<?php

declare(strict_types=1);

namespace Enlivy\Enums\WorkingTime;

use Enlivy\Enums\Concern\EnumValues;

enum BreakTypes: string
{
    use EnumValues;

    case MEAL = 'meal';
    case REST = 'rest';
    case OTHER = 'other';
}
