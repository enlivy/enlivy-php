<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents the Tenant Billing state for an organization in the Enlivy API.
 *
 * The 1:1 row through which Enlivy charges a customer organization for Feature Packs
 * and Capacity Add-ons (the SaaS-subscription side, billed by Enlivy SRL).
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $billing_cycle One of: monthly, yearly
 * @property string|null $trial_used_at
 * @property bool $in_trial
 * @property string|null $billing_schedule_id
 * @property array $feature_pack_subscriptions
 * @property array $capacity_addon_subscriptions
 * @property string $created_at
 * @property string $updated_at
 */
class TenantBilling extends ApiResource
{
    public const ?string OBJECT_NAME = 'tenant_billing';
}
