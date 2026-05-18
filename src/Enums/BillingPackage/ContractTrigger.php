<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

enum ContractTrigger: string
{
    use EnumValues;

    case ON_ACCEPTANCE = 'on_acceptance';
    case ON_FIRST_PAYMENT = 'on_first_payment';
    case ON_FULL_PAYMENT = 'on_full_payment';
}
