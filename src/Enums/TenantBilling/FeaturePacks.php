<?php

declare(strict_types=1);

namespace Enlivy\Enums\TenantBilling;

use Enlivy\Enums\Concern\EnumValues;

enum FeaturePacks: string
{
    use EnumValues;

    case INVOICING = 'invoicing';
    case BANKING = 'banking';
    case CONTRACTS = 'contracts';
    case BILLING_SCHEDULES = 'billing_schedules';
    case SALES = 'sales';
    case GUIDELINES = 'guidelines';
    case PLAYBOOKS = 'playbooks';
    case REPORTS = 'reports';
    case CUSTOM_DOMAIN = 'custom_domain';
    case TASKS = 'tasks';
    case REPORTS_STANDARD = 'reports_standard';
    case REPORTS_UNLIMITED = 'reports_unlimited';
}
