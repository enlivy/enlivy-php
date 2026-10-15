<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum ReplyDues: string
{
    use EnumValues;

    case DUE_SOON = 'due_soon';
    case OVERDUE = 'overdue';
}
