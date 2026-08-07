<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_tax_filing_period_id
 * @property string|null $organization_bank_account_id
 * @property string|null $organization_bank_transaction_id
 * @property string $payment_type
 * @property string $payment_date
 * @property string|null $payment_method
 * @property float $amount
 * @property string|null $currency
 * @property string|null $reference
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class TaxFilingPeriodPayment extends ApiResource
{
    public const ?string OBJECT_NAME = 'tax_filing_period_payment';
}
