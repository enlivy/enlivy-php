<?php

declare(strict_types=1);

namespace Enlivy\Enums\Contract;

use Enlivy\Enums\Concern\EnumValues;

enum SignatureSignSessionConfirmationTypes: string
{
    use EnumValues;

    case EMAIL = 'email';
    case PHONE = 'phone';
    case LEGALLY_BINDING = 'legally_binding';
}
