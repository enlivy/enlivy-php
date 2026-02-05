<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Billing Schedule in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_user_id
 * @property string $name
 * @property string $frequency
 * @property float $amount
 * @property string $currency
 * @property string|null $next_billing_at
 * @property string|null $last_billed_at
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class BillingSchedule extends ApiResource
{
    public const ?string OBJECT_NAME = 'billing_schedule';
}
