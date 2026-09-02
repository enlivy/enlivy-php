<?php

declare(strict_types=1);

namespace Enlivy\Enums\Employment;

use Enlivy\Enums\Concern\EnumValues;

enum Types: string
{
    use EnumValues;

    case PERMANENT = 'permanent';
    case FIXED_TERM = 'fixed_term';
    case PART_TIME = 'part_time';
    case DAY_LABOURER = 'day_labourer';
    case COPYRIGHT = 'copyright';
    case CONTRACTOR = 'contractor';
    case INTERNSHIP = 'internship';
}
