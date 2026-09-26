<?php

declare(strict_types=1);

namespace Enlivy\Enums\Task;

use Enlivy\Enums\Concern\EnumValues;

enum TaskParticipantRoles: string
{
    use EnumValues;

    case ASSIGNEE = 'assignee';
    case FOLLOWER = 'follower';
}
