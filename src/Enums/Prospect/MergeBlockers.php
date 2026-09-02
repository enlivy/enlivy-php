<?php

declare(strict_types=1);

namespace Enlivy\Enums\Prospect;

use Enlivy\Enums\Concern\EnumValues;

enum MergeBlockers: string
{
    use EnumValues;

    case CONFLICTING_LINKED_USER = 'conflicting_linked_user';
    case DIFFERENT_PROJECT = 'different_project';
    case CONFLICTING_OUTCOME = 'conflicting_outcome';
}
