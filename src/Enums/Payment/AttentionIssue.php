<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payment;

use Enlivy\Enums\Concern\EnumValues;

enum AttentionIssue: string
{
    use EnumValues;

    case DOUBLE_PAYMENT = 'double_payment';
    case AMOUNT_MISMATCH = 'amount_mismatch';
    case CHECKOUT_NOT_SETTLED = 'checkout_not_settled';
    case PROFORMA_PART_PAID = 'proforma_part_paid';
    case PAYMENT_ON_CANCELLED_PROFORMA = 'payment_on_cancelled_proforma';
}
