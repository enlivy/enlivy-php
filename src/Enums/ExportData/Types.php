<?php

declare(strict_types=1);

namespace Enlivy\Enums\ExportData;

use Enlivy\Enums\Concern\EnumValues;

enum Types: string
{
    use EnumValues;

    case FULL = 'full';
    case ACCOUNTING_SAGA = 'accounting_saga';
    case WORKING_TIME_TIMESHEET = 'working_time_timesheet';
    case PAYROLL_HANDOFF = 'payroll_handoff';
}
