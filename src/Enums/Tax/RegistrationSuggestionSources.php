<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum RegistrationSuggestionSources: string
{
    use EnumValues;

    case COMPANIES_API = 'companies_api';
    case ORGANIZATION_INFORMATION = 'organization_information';
    case COUNTRY_PACK = 'country_pack';
}
