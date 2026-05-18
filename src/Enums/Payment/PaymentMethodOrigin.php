<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payment;

use Enlivy\Enums\Concern\EnumValues;

enum PaymentMethodOrigin: string
{
    use EnumValues;

    case PLATFORM_SETUP = 'platform_setup';
    case IMPORTED_OFF_PLATFORM = 'imported_off_platform';
    case MANUAL_ENTRY = 'manual_entry';
}
