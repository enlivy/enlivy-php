<?php

declare(strict_types=1);

namespace Enlivy\Enums\PageLayout;

use Enlivy\Enums\Concern\EnumValues;

enum Pages: string
{
    use EnumValues;

    case DASHBOARD = 'dashboard';
    case USER_VIEW = 'user_view';
    case HELPDESK_OVERVIEW = 'helpdesk_overview';
    case PROSPECT_OVERVIEW = 'prospect_overview';
    case HELPDESK_CONVERSATION_VIEW = 'helpdesk_conversation_view';
    case REPORT_VIEW = 'report_view';
    case INVOICE_VIEW = 'invoice_view';
    case APP_OVERVIEW = 'app_overview';
}
