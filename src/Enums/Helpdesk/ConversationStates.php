<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum ConversationStates: string
{
    use EnumValues;

    case OPEN = 'open';
    case PENDING = 'pending';
    case SNOOZED = 'snoozed';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case SPAM = 'spam';
}
