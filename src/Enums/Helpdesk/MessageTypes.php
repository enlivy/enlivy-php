<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum MessageTypes: string
{
    use EnumValues;

    case INCOMING = 'incoming';
    case OUTGOING = 'outgoing';
    case NOTE = 'note';
    case ACTIVITY = 'activity';
}
