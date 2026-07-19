<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum TaxEventSourceTypes: string
{
    use EnumValues;

    case INVOICE = 'invoice';
    case RECEIPT = 'receipt';
    case CUSTOMS = 'customs';
    case BANK_CORRECTION = 'bank_correction';
    case AUTHORITY = 'authority';
    case MANUAL = 'manual';
    case BASELINE = 'baseline';
}
