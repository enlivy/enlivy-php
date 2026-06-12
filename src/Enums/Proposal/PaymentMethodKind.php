<?php

declare(strict_types=1);

namespace Enlivy\Enums\Proposal;

use Enlivy\Enums\Concern\EnumValues;

enum PaymentMethodKind: string
{
    use EnumValues;

    case BANK_TRANSFER = 'bank_transfer';
    case CARD = 'card';
}
