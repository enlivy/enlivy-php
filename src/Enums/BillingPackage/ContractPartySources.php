<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

enum ContractPartySources: string
{
    use EnumValues;

    case SENDER = 'sender';
    case RECEIVER = 'receiver';
    case ASSIGNED = 'assigned';
    case STATED = 'stated';
}
