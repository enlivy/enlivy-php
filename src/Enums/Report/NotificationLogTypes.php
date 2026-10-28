<?php

declare(strict_types=1);

namespace Enlivy\Enums\Report;

use Enlivy\Enums\Concern\EnumValues;

enum NotificationLogTypes: string
{
    use EnumValues;

    case EMAIL = 'email';
}
