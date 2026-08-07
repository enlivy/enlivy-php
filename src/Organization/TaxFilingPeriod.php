<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_tax_filing_jurisdiction_id
 * @property string $organization_tax_type_id
 * @property string $period_start
 * @property string $period_end
 * @property float|null $tax_collectible
 * @property float|null $tax_paid_input
 * @property float|null $tax_withheld
 * @property float|null $adjustments
 * @property float|null $credit_carried_forward
 * @property float|null $credit_applied_from_previous
 * @property float|null $net_liability
 * @property string|null $filing_due_date
 * @property string|null $payment_due_date
 * @property string|null $currency
 * @property string $status
 * @property bool $is_liability_cleared
 * @property int|null $filing_version
 * @property string|null $filed_at
 * @property string|null $filing_reference
 * @property float|null $computed_tax_collectible
 * @property float|null $computed_tax_paid_input
 * @property float|null $computed_net_liability
 * @property string|null $computed_at
 * @property string|null $organization_tax_registration_id
 * @property string|null $pack_obligation_key
 * @property string|null $auto_imported_from
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class TaxFilingPeriod extends ApiResource
{
    public const ?string OBJECT_NAME = 'tax_filing_period';
}
