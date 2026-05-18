<?php

declare(strict_types=1);

namespace Enlivy\Enums\UserClientPortal;

use Enlivy\Enums\Concern\EnumValues;

enum SessionStatuses: string
{
    use EnumValues;

    case PENDING = 'pending';
    case EMAIL_VERIFICATION_SENT = 'email_verification_sent';
    case SUCCESS = 'success';
    case EXPIRED = 'expired';
}
