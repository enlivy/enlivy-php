<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Billing Package Subscription Term Item — per-(term, group item) availability and pricing.
 *
 * `prices` is read back as a currency-keyed map of decimal-string amounts,
 * e.g. `{"EUR": {"price": "59.00", "discount": "5.00"}}`; on write it is a list
 * of `{currency, price, discount}` rows nested under the package request.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_billing_package_subscription_term_id
 * @property string $organization_billing_package_group_item_id
 * @property bool $is_available
 * @property array|null $prices
 * @property array|null $unit_lang_map
 * @property array|null $invoice_schema_map
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class BillingPackageSubscriptionTermItem extends ApiResource
{
    public const ?string OBJECT_NAME = 'subscription_term_item';
}
