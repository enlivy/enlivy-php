<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string|null $description
 * @property int $member_count
 * @property int $lead_count
 * @property array|null $members
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class HelpdeskTeam extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_team';
}
