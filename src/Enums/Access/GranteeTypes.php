<?php

declare(strict_types=1);

namespace Enlivy\Enums\Access;

use Enlivy\Enums\Concern\EnumValues;

enum GranteeTypes: string
{
    use EnumValues;

    case USER_ROLE = 'user_role';
    case PROJECT_ROLE = 'project_role';
    case PROJECT_MEMBER = 'project_member';
    case PERSON = 'person';
}
