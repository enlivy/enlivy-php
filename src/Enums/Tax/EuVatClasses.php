<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum EuVatClasses: string
{
    use EnumValues;

    case CBC_AE_ID = 'AE';
    case CBC_E_ID = 'E';
    case CBC_S_ID = 'S';
    case CBC_Z_ID = 'Z';
    case CBC_G_ID = 'G';
    case CBC_O_ID = 'O';
    case CBC_K_ID = 'K';
    case CBC_L_ID = 'L';
    case CBC_M_ID = 'M';
    case CBC_B_ID = 'B';
}
