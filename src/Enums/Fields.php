<?php

declare(strict_types=1);

namespace Enlivy\Enums;

use Enlivy\Enums\Concern\EnumValues;

enum Fields: string
{
    use EnumValues;

    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case SELECT = 'select';
    case EDITOR = 'editor';
    case NUMBER = 'number';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case TIMESHEET = 'timesheet';
    case TIMESHEET_WITH_TEXT = 'timesheet_with_text';
    case TRUE_FALSE = 'true_false';
    case ORGANIZATION_USER = 'organization_user';
    case ORGANIZATION_USER_ROLE = 'organization_user_role';
    case NUMBER_CURRENCY = 'number_currency';
    case NUMBER_PERCENTAGE = 'number_percentage';
}
