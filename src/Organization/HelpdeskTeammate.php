<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $organization_user_id
 * @property string|null $display_name
 * @property string|null $timezone
 * @property string $name
 * @property bool $is_available
 * @property string|null $away_until
 * @property bool $is_present
 * @property bool $away_reassign_enabled
 * @property array|null $schedule
 * @property string $created_at
 * @property string $updated_at
 */
class HelpdeskTeammate extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_teammate';
}
