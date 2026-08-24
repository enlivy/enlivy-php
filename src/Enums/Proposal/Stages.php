<?php

declare(strict_types=1);

namespace Enlivy\Enums\Proposal;

use Enlivy\Enums\Concern\EnumValues;

enum Stages: string
{
    use EnumValues;

    case DRAFTING = 'drafting';
    case AWAITING_ACCEPTANCE = 'awaiting_acceptance';
    case AWAITING_CONTRACT = 'awaiting_contract';
    case AWAITING_SIGNATURE = 'awaiting_signature';
    case AWAITING_PAYMENT = 'awaiting_payment';
    case CLOSED = 'closed';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
}
