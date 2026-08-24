<?php

declare(strict_types=1);

namespace Enlivy\Enums\Proposal;

use Enlivy\Enums\Concern\EnumValues;

enum NotificationLogTypes: string
{
    use EnumValues;

    case EMAIL = 'email';
    case EMAIL_SELLER_VIEWED = 'email_seller_viewed';
    case EMAIL_SELLER_ACCEPTED = 'email_seller_accepted';
    case EMAIL_SELLER_REJECTED = 'email_seller_rejected';
    case EMAIL_SELLER_EXPIRED = 'email_seller_expired';
    case EMAIL_SELLER_CONTRACT_GENERATED = 'email_seller_contract_generated';
}
