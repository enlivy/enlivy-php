<?php

declare(strict_types=1);

namespace Enlivy\Enums\Contract;

use Enlivy\Enums\Concern\EnumValues;

enum SignatureSignSessionSignatureTypes: string
{
    use EnumValues;

    case FILE_CLASSIC = 'file_classic';
    case FILE_ELECTRONIC = 'file_electronic';
    case DRAW = 'draw';
    case CHECKBOX = 'checkbox';
}
