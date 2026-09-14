<?php

declare(strict_types=1);

namespace Enlivy\Enums\Project;

use Enlivy\Enums\Concern\EnumValues;

enum ProspectAccessScopes: string
{
    use EnumValues;

    case NONE = 'none';
    case OWN = 'own';
    case OWN_AND_UNASSIGNED = 'own_and_unassigned';
    case ALL = 'all';
}
