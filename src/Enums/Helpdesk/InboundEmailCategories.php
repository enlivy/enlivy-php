<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum InboundEmailCategories: string
{
    use EnumValues;

    case UNCLASSIFIED = 'unclassified';
    case HELD = 'held';
    case TICKETS = 'tickets';
    case MARKETING = 'marketing';
    case AUTOMATED = 'automated';
    case SPAM = 'spam';
    case FAILED = 'failed';
}
