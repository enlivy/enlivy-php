<?php

declare(strict_types=1);

namespace Enlivy\Enums\BankAccount;

use Enlivy\Enums\Concern\EnumValues;

enum StatementFormats: string
{
    use EnumValues;

    case PDF = 'pdf';
    case MT940 = 'mt940';
    case CAMT_053 = 'camt_053';
    case CSV = 'csv';
    case SPREADSHEET = 'spreadsheet';
    case OTHER = 'other';
}
