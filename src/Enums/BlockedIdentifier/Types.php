<?php

declare(strict_types=1);

namespace Enlivy\Enums\BlockedIdentifier;

use Enlivy\Enums\Concern\EnumValues;

enum Types: string
{
    use EnumValues;

    case EMAIL = 'email';
    case EMAIL_DOMAIN = 'email_domain';
    case PHONE_NUMBER = 'phone_number';
}
