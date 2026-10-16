<?php

declare(strict_types=1);

namespace Enlivy\Enums\Report;

use Enlivy\Enums\Concern\EnumValues;

enum Statuses: string
{
    use EnumValues;

    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case ACKNOWLEDGED = 'acknowledged';
}
