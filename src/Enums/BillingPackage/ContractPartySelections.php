<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

enum ContractPartySelections: string
{
    use EnumValues;

    case STANDARD = 'standard';
    case CUSTOM = 'custom';
}
