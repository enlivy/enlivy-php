<?php

declare(strict_types=1);

namespace Enlivy\Enums\Receipt;

use Enlivy\Enums\Concern\EnumValues;

enum Sources: string
{
    use EnumValues;

    case UPLOADED = 'uploaded';
    case GENERATED = 'generated';
}
