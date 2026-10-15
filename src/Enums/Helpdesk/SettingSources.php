<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum SettingSources: string
{
    use EnumValues;

    case INBOX = 'inbox';
    case ORGANIZATION = 'organization';
    case PLATFORM = 'platform';
}
