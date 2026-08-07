<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

enum PortalDiscoveryMode: string
{
    use EnumValues;

    case DISABLED = 'disabled';
    case REQUEST = 'request';
    case CHECKOUT = 'checkout';
}
