<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum ConversationWaitingOnParties: string
{
    use EnumValues;

    case TEAM = 'team';
    case CUSTOMER = 'customer';
}
