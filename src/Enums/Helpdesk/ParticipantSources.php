<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum ParticipantSources: string
{
    use EnumValues;

    case EMBED = 'embed';
    case CUSTOMER = 'customer';
    case AUTHOR = 'author';
    case EMAIL = 'email';
    case TEAMMATE = 'teammate';
}
