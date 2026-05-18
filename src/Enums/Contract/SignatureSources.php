<?php

declare(strict_types=1);

namespace Enlivy\Enums\Contract;

use Enlivy\Enums\Concern\EnumValues;

enum SignatureSources: string
{
    use EnumValues;

    case ADMIN_PANEL = 'admin_panel';
    case USER_FLOW = 'user_flow';
}
