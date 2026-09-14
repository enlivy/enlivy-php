<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum ProactiveMessageConditionTypes: string
{
    use EnumValues;

    case PAGE_VISITED = 'page_visited';
    case TIME_ON_PAGE = 'time_on_page';
    case PAGES_VISITED_ALL = 'pages_visited_all';
    case PAGES_VISITED_ANY = 'pages_visited_any';
    case TOTAL_PAGE_VIEWS = 'total_page_views';
}
