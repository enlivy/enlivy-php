<?php

declare(strict_types=1);

namespace Enlivy\Enums\NetworkExchange;

use Enlivy\Enums\Concern\EnumValues;

enum Statuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case CHANGE_REQUIRED = 'change_required';
    case PROCESSING = 'processing';
    case REJECTED = 'rejected';
    case REJECTED_AT_EXCHANGE = 'rejected_at_exchange';
    case FAILED = 'failed';
    case FAILED_CREDENTIALS_EXPIRED = 'failed_credentials_expired';
    case SUCCESS = 'success';
    case SUCCESS_PENDING_ARCHIVE = 'success_pending_archive';
    case SUCCESS_IN_EXCHANGE_QUEUE = 'success_in_exchange_queue';
}
