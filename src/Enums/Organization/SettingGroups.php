<?php

declare(strict_types=1);

namespace Enlivy\Enums\Organization;

use Enlivy\Enums\Concern\EnumValues;

enum SettingGroups: string
{
    use EnumValues;

    case INVOICING = 'invoicing';
    case INVOICE_PAYMENT_REMINDER = 'invoice_payment_reminder';
    case TAXES = 'taxes';
    case RECEIPTS = 'receipts';
    case BANKING = 'banking';
    case CONTRACTS = 'contracts';
    case SALES = 'sales';
    case USERS = 'users';
    case BLOCKED_IDENTIFIERS = 'blocked_identifiers';
    case EMAIL = 'email';
    case STRIPE_CONNECT = 'stripe_connect';
    case NETWORK_EXCHANGE = 'network_exchange';
    case NETWORK_EXCHANGE_AUTO_PUSH = 'network_exchange_auto_push';
    case PERSONALIZATION = 'personalization';
}
