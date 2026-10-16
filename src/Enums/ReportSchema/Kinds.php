<?php

declare(strict_types=1);

namespace Enlivy\Enums\ReportSchema;

use Enlivy\Enums\Concern\EnumValues;

enum Kinds: string
{
    use EnumValues;

    case DAILY_LOG = 'daily_log';
    case WORK_REPORT = 'work_report';
    case MEETING_NOTES = 'meeting_notes';
    case ACCEPTANCE = 'acceptance';
    case FEEDBACK = 'feedback';
    case GENERAL = 'general';
}
