<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Organization User Payment Method in the Enlivy API.
 *
 * A stored payment instrument (e.g. a Stripe card) belonging to an organization user.
 * Used for off-session invoice charging and portal-initiated payments.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_user_id
 * @property string $provider
 * @property string $type
 * @property string $origin
 * @property array|null $origin_metadata
 * @property bool $is_default
 * @property string|null $alias
 * @property string $status
 * @property string|null $notes
 * @property array|null $display_metadata
 * @property string|null $expires_at
 * @property bool $is_chargeable
 * @property bool $is_expired
 * @property string|null $lookup_key
 * @property string|null $created_by_user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class UserPaymentMethod extends ApiResource
{
    public const ?string OBJECT_NAME = 'user_payment_method';
}
