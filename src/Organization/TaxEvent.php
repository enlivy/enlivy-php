<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Tax Event (a single tax-relevant transaction line) in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $direction
 * @property string $source_type
 * @property string|null $source_id
 * @property string|null $source_line_id
 * @property string $tax_point_date
 * @property string|null $document_date
 * @property string|null $country_code
 * @property string|null $scheme
 * @property string|null $regime
 * @property bool $is_reverse_charge
 * @property bool $is_self_assessed
 * @property string|null $category
 * @property float|null $rate
 * @property float $base_amount
 * @property float $tax_amount
 * @property string|null $currency
 * @property string|null $source_currency
 * @property float|null $source_base_amount
 * @property float|null $source_tax_amount
 * @property float|null $applied_exchange_rate
 * @property string|null $applied_exchange_rate_provider
 * @property string|null $counterparty_name
 * @property string|null $counterparty_country_code
 * @property string|null $counterparty_subdivision
 * @property string|null $counterparty_vat_number
 * @property bool|null $counterparty_is_business
 * @property bool|null $counterparty_is_vat_registered
 * @property string|null $supply_type
 * @property float|null $deduction_rate
 * @property int|null $transaction_count
 * @property string|null $organization_tax_filing_jurisdiction_id
 * @property string|null $organization_tax_type_id
 * @property string|null $organization_tax_filing_period_id
 * @property string|null $created_by_user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class TaxEvent extends ApiResource
{
    public const ?string OBJECT_NAME = 'tax_event';
}
