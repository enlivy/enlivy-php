<?php

declare(strict_types=1);

namespace Enlivy\Enums\Project;

use Enlivy\Enums\Concern\EnumValues;

enum MemberRoles: string
{
    use EnumValues;

    case TEAM_MEMBER = 'team_member';
    case CONTRACTOR = 'contractor';
    case CLIENT = 'client';
    case PROSPECT = 'prospect';
}
