<?php

declare(strict_types=1);

namespace Enlivy\Enums\Stripe;

use Enlivy\Enums\Concern\EnumValues;

enum ConnectStatuses: string
{
    use EnumValues;

    case SUCCESS = 'success';
    case ERROR_INVALID_REQUEST = 'error_invalid_request';
    case ERROR_STRIPE_REQUEST_FAILED = 'error_stripe_request_failed';
    case ERROR_INVALID_SETTINGS = 'error_invalid_settings';
    case ERROR_FATAL = 'error_fatal';
}
