<?php

declare(strict_types=1);

namespace Enlivy\Enums\Contract;

use Enlivy\Enums\Concern\EnumValues;

enum States: string
{
    use EnumValues;

    case DRAFT = 'draft';
    case ACTION_REQUIRED = 'action_required';
    case ACCEPTED = 'accepted';
    case BREACH = 'breach';
    case TERMINATED = 'terminated';
}
