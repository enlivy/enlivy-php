<?php

declare(strict_types=1);

namespace Enlivy\Enums\EventTrail;

use Enlivy\Enums\Concern\EnumValues;

enum EventType: string
{
    use EnumValues;

    case CREATED = 'created';
    case UPDATED = 'updated';
    case STATUS_CHANGED = 'status_changed';
    case DELETED = 'deleted';
    case RESTORED = 'restored';
    case FINALIZED = 'finalized';
    case CHARGED = 'charged';
    case CHARGE_FAILED = 'charge_failed';
    case REFUNDED = 'refunded';
    case REFUND_FAILED = 'refund_failed';
    case PAYMENT_METHOD_CHANGED = 'payment_method_changed';
    case CANCELLED = 'cancelled';
    case PAYMENT_GENERATED = 'payment_generated';
    case PRORATED_ADJUSTMENT_GENERATED = 'prorated_adjustment_generated';
    case LINE_ITEM_CHANGED = 'line_item_changed';
    case TAX_BREAKDOWN_CHANGED = 'tax_breakdown_changed';
    case PHASE_CHANGED = 'phase_changed';
    case SCHEDULED_PAYMENT_CHANGED = 'scheduled_payment_changed';
}
