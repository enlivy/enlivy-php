<?php

declare(strict_types=1);

namespace Enlivy\Enums\Organization;

use Enlivy\Enums\Concern\EnumValues;

enum FeatureGroups: string
{
    use EnumValues;

    case INVOICING = 'invoicing';
    case BANKING = 'banking';
    case CONTRACTS = 'contracts';
    case HR = 'hr';
    case PROJECTS = 'projects';
    case SALES = 'sales';
    case GUIDELINES = 'guidelines';
    case PLAYBOOKS = 'playbooks';
    case REPORTS = 'reports';
}
