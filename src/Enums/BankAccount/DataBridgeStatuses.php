<?php

declare(strict_types=1);

namespace Enlivy\Enums\BankAccount;

use Enlivy\Enums\Concern\EnumValues;

enum DataBridgeStatuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case REQUISITIONS = 'requisitions';
    case PAIRED = 'paired';
    case UNPAIRED = 'unpaired';
    case EXPIRED = 'expired';
    case DELETED = 'deleted';
    case FAILED_DELETED = 'failed_deleted';
}
