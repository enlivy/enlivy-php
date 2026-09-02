<?php

declare(strict_types=1);

namespace Enlivy\Enums\Prospect;

use Enlivy\Enums\Concern\EnumValues;

enum DuplicateSignals: string
{
    use EnumValues;

    case LINKED_ORGANIZATION_USER = 'linked_organization_user';
    case EMAIL = 'email';
    case PHONE_NUMBER = 'phone_number';
    case NAME = 'name';
}
