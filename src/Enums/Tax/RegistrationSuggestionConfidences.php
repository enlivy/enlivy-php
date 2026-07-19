<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum RegistrationSuggestionConfidences: string
{
    use EnumValues;

    case VERIFIED = 'verified';
    case STORED_IDENTIFIER = 'stored_identifier';
    case COUNTRY_DEFAULT = 'country_default';
}
