<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum ValidationSources: string
{
    use EnumValues;

    case VIES = 'vies';
    case ANAF = 'anaf';
    case MANUAL = 'manual';
    case COMPANIES_API = 'companies_api';
}
