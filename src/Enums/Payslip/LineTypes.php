<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payslip;

use Enlivy\Enums\Concern\EnumValues;

enum LineTypes: string
{
    use EnumValues;

    case INFORMATIONAL = 'informational';
    case EARNING = 'earning';
    case EMPLOYEE_CONTRIBUTION = 'employee_contribution';
    case TAX_RELIEF = 'tax_relief';
    case TAX = 'tax';
    case POST_TAX_DEDUCTION = 'post_tax_deduction';
    case EMPLOYER_CONTRIBUTION = 'employer_contribution';
}
