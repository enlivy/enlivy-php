<?php

declare(strict_types=1);

namespace Enlivy\Enums\Proposal;

use Enlivy\Enums\Concern\EnumValues;

enum StageActors: string
{
    use EnumValues;

    case ORGANIZATION = 'organization';
    case CUSTOMER = 'customer';
    case THIRD_PARTY = 'third_party';
    case SEVERAL = 'several';
}
