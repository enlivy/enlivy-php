<?php

declare(strict_types=1);

namespace Enlivy\Enums\Employment;

use Enlivy\Enums\Concern\EnumValues;

enum AgreementTypes: string
{
    use EnumValues;

    case WEEKLY_HOURS_OPT_OUT = 'weekly_hours_opt_out';
    case WORKING_TIME_EXEMPTION = 'working_time_exemption';
}
