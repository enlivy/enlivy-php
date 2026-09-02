<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum MappingSuggestionOutcomes: string
{
    use EnumValues;

    case CANDIDATES = 'candidates';
    case NO_TAX = 'no_tax';
    case UNMATCHED = 'unmatched';
}
