<?php

declare(strict_types=1);

namespace Enlivy\Enums\Invoice;

use Enlivy\Enums\Concern\EnumValues;

enum DownloadTypes: string
{
    use EnumValues;

    case PDF = 'pdf';
    case AT_GOV_XML = 'at_gov_xml';
    case AT_NAT_XML = 'at_nat_xml';
    case ES_XML = 'es_xml';
    case NL_XML = 'nl_xml';
    case IT_XML = 'it_xml';
    case RO_XML = 'ro_xml';
    case PEPPOL = 'peppol';
}
