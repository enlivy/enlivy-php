<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_billing_package_id
 * @property array|null $name_lang_map
 * @property array|null $description_lang_map
 * @property string $primary_currency
 * @property string $frequency
 * @property string|null $due_date_type
 * @property int|null $due_date_days
 * @property int|null $trial_days
 * @property int|null $contract_min_months
 * @property int|null $cancellation_notice_days
 * @property string|null $proration_policy
 * @property string $status
 * @property bool $is_default
 * @property int $order
 * @property string|null $deleted_by_user_id
 * @property string|null $deleted_at
 * @property string $created_at
 * @property string $updated_at
 */
class BillingPackageSubscriptionTerm extends ApiResource
{
    public const ?string OBJECT_NAME = 'subscription_term';
}
