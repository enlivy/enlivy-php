<?php

declare(strict_types=1);

namespace Enlivy\Enums\Access;

use Enlivy\Enums\Concern\EnumValues;

enum ResourceTypes: string
{
    use EnumValues;

    case REPORT_SCHEMA = 'report_schema';
    case PLAYBOOK = 'playbook';
    case GUIDELINE = 'guideline';
    case PROSPECT_PIPELINE = 'prospect_pipeline';
}
