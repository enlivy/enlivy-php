<?php

declare(strict_types=1);

namespace Enlivy\Enums\TenantBilling;

use Enlivy\Enums\Concern\EnumValues;

enum ChangeSetTypes: string
{
    use EnumValues;

    case PURCHASE_PACK = 'purchase_pack';
    case CANCEL_PACK = 'cancel_pack';
    case SET_ADDON_TIER = 'set_addon_tier';
    case SET_ADDON_QUANTITY = 'set_addon_quantity';
}
