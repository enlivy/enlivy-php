<?php

declare(strict_types=1);

namespace Enlivy\Enums\CheckoutSession;

use Enlivy\Enums\Concern\EnumValues;

enum Modes: string
{
    use EnumValues;

    case PAYMENT = 'payment';
    case SETUP = 'setup';
}
