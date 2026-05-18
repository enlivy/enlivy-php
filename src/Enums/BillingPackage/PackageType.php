<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

enum PackageType: string
{
    use EnumValues;

    case SUBSCRIPTION = 'subscription';
    case ONE_TIME = 'one_time';
}
