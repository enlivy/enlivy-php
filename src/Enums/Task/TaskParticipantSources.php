<?php

declare(strict_types=1);

namespace Enlivy\Enums\Task;

use Enlivy\Enums\Concern\EnumValues;

enum TaskParticipantSources: string
{
    use EnumValues;

    case CREATOR = 'creator';
    case ASSIGNER = 'assigner';
    case ASSIGNED = 'assigned';
    case MENTIONED = 'mentioned';
    case COMMENTED = 'commented';
    case MANUAL = 'manual';
}
