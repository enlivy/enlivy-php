<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum MappingSuggestionBases: string
{
    use EnumValues;

    case SUPPLIER_HISTORY = 'supplier_history';
    case ORGANIZATION_FREQUENCY = 'organization_frequency';
    case CATEGORY_MATCH = 'category_match';
    case RATE_MATCH = 'rate_match';
    case FORWARD_RESOLUTION = 'forward_resolution';
}
