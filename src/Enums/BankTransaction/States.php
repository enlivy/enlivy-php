<?php

declare(strict_types=1);

namespace Enlivy\Enums\BankTransaction;

use Enlivy\Enums\Concern\EnumValues;

enum States: string
{
    use EnumValues;

    case BACKLOG = 'backlog';
    case COMPLETED = 'completed';
    case UNBALANCED = 'unbalanced';
    case TRASHED = 'trashed';
}
