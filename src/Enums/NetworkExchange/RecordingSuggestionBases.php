<?php

declare(strict_types=1);

namespace Enlivy\Enums\NetworkExchange;

use Enlivy\Enums\Concern\EnumValues;

enum RecordingSuggestionBases: string
{
    use EnumValues;

    case PAYMENT_HISTORY = 'payment_history';
    case RECORDED_HISTORY = 'recorded_history';
    case IDENTIFIER_MATCH = 'identifier_match';
    case EMAIL_MATCH = 'email_match';
    case NAME_MATCH = 'name_match';
    case ORGANIZATION_OWNER = 'organization_owner';
    case CURRENCY_HISTORY = 'currency_history';
}
