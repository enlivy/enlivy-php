<?php

declare(strict_types=1);

namespace Enlivy\Enums\BillingPackage;

use Enlivy\Enums\Concern\EnumValues;

enum ContractSectionContentSources: string
{
    use EnumValues;

    case STANDARD = 'standard';
    case REUSABLE_CONTENT = 'reusable_content';
    case PURCHASE_ITEMS = 'purchase_items';
    case PURCHASE_TERMS = 'purchase_terms';
    case PURCHASE_SUMMARY = 'purchase_summary';
    case PRODUCT_LIST = 'product_list';
    case PURCHASED_PRODUCT_LIST = 'purchased_product_list';
}
