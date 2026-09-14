<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum InboundEmailInterpretations: string
{
    use EnumValues;

    case MATCHED = 'matched';
    case NEW_CONVERSATION = 'new_conversation';
    case UNMATCHED = 'unmatched';
    case FAILED = 'failed';
    case BOUNCE = 'bounce';
    case AUTO_REPLY = 'auto_reply';
    case SPAM = 'spam';
    case BLOCKED = 'blocked';
    case DISCARDED = 'discarded';
}
