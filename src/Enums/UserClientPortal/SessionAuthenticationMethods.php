<?php

declare(strict_types=1);

namespace Enlivy\Enums\UserClientPortal;

use Enlivy\Enums\Concern\EnumValues;

enum SessionAuthenticationMethods: string
{
    use EnumValues;

    case EMAIL = 'email';
    case PHONE = 'phone';
    case MAGIC_AUTHENTICATION = 'magic_authentication';
}
