<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Contract Status in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string $color
 * @property int $order
 * @property bool $is_default
 * @property bool $is_active
 * @property bool $is_signed
 * @property string $created_at
 * @property string $updated_at
 */
class ContractStatus extends ApiResource
{
    public const ?string OBJECT_NAME = 'contract_status';
}
