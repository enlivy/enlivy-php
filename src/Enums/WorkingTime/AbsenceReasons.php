<?php

declare(strict_types=1);

namespace Enlivy\Enums\WorkingTime;

use Enlivy\Enums\Concern\EnumValues;

enum AbsenceReasons: string
{
    use EnumValues;

    case ANNUAL_LEAVE = 'annual_leave';
    case PAID_EVENT_LEAVE = 'paid_event_leave';
    case UNPAID_LEAVE = 'unpaid_leave';
    case SICK_LEAVE = 'sick_leave';
    case WORK_ACCIDENT = 'work_accident';
    case MATERNITY_LEAVE = 'maternity_leave';
    case PATERNITY_LEAVE = 'paternity_leave';
    case PARENTAL_LEAVE = 'parental_leave';
    case CARERS_LEAVE = 'carers_leave';
    case UNEXCUSED_ABSENCE = 'unexcused_absence';
}
