<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum TeamMemberRoles: string
{
    use EnumValues;

    case MEMBER = 'member';
    case LEAD = 'lead';
}
