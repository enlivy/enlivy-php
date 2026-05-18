<?php

declare(strict_types=1);

namespace Enlivy\Enums\BankTransaction;

use Enlivy\Enums\Concern\EnumValues;

enum States: string
{
    use EnumValues;

    case BACKLOG = 'backlog';
    case CLASSIFIED = 'classified';
    case CONNECTED = 'connected';
    case CONNECTED_PARTIALLY = 'connected_partially';
    case DANGER = 'danger';
    case TRASHED = 'trashed';
}
