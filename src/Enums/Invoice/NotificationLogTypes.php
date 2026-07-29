<?php

declare(strict_types=1);

namespace Enlivy\Enums\Invoice;

use Enlivy\Enums\Concern\EnumValues;

enum NotificationLogTypes: string
{
    use EnumValues;

    case NETWORK_EXCHANGE_AUTO_PUSH = 'network_exchange_auto_push';
    case EMAIL = 'email';
    case EMAIL_AUTO_SEND = 'email_auto_send';
    case EMAIL_REMINDER_UPCOMING = 'email_reminder_upcoming';
    case EMAIL_REMINDER_OVERDUE = 'email_reminder_overdue';
}
