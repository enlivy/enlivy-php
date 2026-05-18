<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payment;

use Enlivy\Enums\Concern\EnumValues;

enum ChargeSource: string
{
    use EnumValues;

    case CRON = 'cron';
    case ADMIN = 'admin';
    case PORTAL_USER = 'portal_user';
    case STANDALONE = 'standalone';
    case WEBHOOK = 'webhook';
}
