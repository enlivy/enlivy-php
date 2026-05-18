<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingSchedule;

use Enlivy\Enums\Concern\EnumValues;

enum PhaseFrequency: string
{
    use EnumValues;

    case WEEKLY = 'weekly';
    case BIWEEKLY = 'biweekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
}
