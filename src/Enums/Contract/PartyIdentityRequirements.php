<?php

declare(strict_types=1);

namespace Enlivy\Enums\Contract;

use Enlivy\Enums\Concern\EnumValues;

enum PartyIdentityRequirements: string
{
    use EnumValues;

    case CONTACT = 'contact';
    case IDENTITY_DOCUMENT = 'identity_document';
    case CIVIL_REGISTRY = 'civil_registry';
}
