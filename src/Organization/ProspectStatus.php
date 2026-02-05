<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Prospect Status in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string $color
 * @property int $order
 * @property bool $is_default
 * @property bool $is_won
 * @property bool $is_lost
 * @property string $created_at
 * @property string $updated_at
 */
class ProspectStatus extends ApiResource
{
    public const ?string OBJECT_NAME = 'prospect_status';
}
