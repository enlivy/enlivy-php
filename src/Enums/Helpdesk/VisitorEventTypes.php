<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum VisitorEventTypes: string
{
    use EnumValues;

    case ARRIVAL = 'arrival';
    case PROACTIVE_MESSAGE_SHOWN = 'proactive_message_shown';
    case PROACTIVE_MESSAGE_CLICKED = 'proactive_message_clicked';
    case PROACTIVE_MESSAGE_DISMISSED = 'proactive_message_dismissed';
    case PROACTIVE_MESSAGE_CONVERTED = 'proactive_message_converted';
}
