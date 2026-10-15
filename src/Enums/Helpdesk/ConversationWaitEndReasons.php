<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum ConversationWaitEndReasons: string
{
    use EnumValues;

    case ANSWERED = 'answered';
    case HANDED_TO_CUSTOMER = 'handed_to_customer';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case MERGED = 'merged';
    case SPAM = 'spam';
    case DELETED = 'deleted';
}
