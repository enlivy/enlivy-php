<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum ConversationSources: string
{
    use EnumValues;

    case WIDGET = 'widget';
    case EMAIL = 'email';
    case PORTAL = 'portal';
    case API = 'api';
}
