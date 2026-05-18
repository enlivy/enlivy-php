<?php

declare(strict_types=1);

namespace Enlivy\Enums\Contract;

use Enlivy\Enums\Concern\EnumValues;

enum StateActionRequiredTypes: string
{
    use EnumValues;

    case RECEIVER_RECEIPT_CONFIRMATION_REQUIRED = 'receiver_receipt_confirmation_required';
    case SENDER_SIGNATURE_REQUIRED = 'sender_signature_required';
    case SENDER_RECEIPT_CONFIRMATION_REQUIRED = 'sender_receipt_confirmation_required';
    case PARTIES_SIGNATURES_REQUIRED = 'parties_signatures_required';
}
