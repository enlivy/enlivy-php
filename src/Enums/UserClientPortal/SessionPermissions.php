<?php

declare(strict_types=1);

namespace Enlivy\Enums\UserClientPortal;

use Enlivy\Enums\Concern\EnumValues;

enum SessionPermissions: string
{
    use EnumValues;

    case INVOICES = 'invoices';
    case NETWORK_EXCHANGES = 'network_exchanges';
    case RECEIPTS = 'receipts';
    case CONTRACTS = 'contracts';
    case REPORTS = 'reports';
    case PAYMENT_METHODS = 'payment_methods';
    case HELPDESK = 'helpdesk';
    case WORKING_TIME = 'working_time';
    case PAYSLIPS = 'payslips';
    case KNOWLEDGE = 'knowledge';
    case BILLING_SCHEDULES = 'billing_schedules';
    case PROJECTS = 'projects';
    case PROSPECTS = 'prospects';
    case CHECKOUT = 'checkout';
}
