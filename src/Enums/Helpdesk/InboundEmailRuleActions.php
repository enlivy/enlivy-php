<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum InboundEmailRuleActions: string
{
    use EnumValues;

    case ROUTE = 'route';
    case DISCARD = 'discard';
    case TRUST = 'trust';
}
