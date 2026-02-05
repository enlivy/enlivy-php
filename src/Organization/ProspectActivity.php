<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Prospect Activity in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_prospect_id
 * @property string|null $performed_by_organization_user_id
 * @property string|null $organization_report_id
 * @property string|null $organization_prospect_status_path_id
 * @property string|null $created_by_user_id
 * @property string $type
 * @property string|null $description
 * @property string|null $activity_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class ProspectActivity extends ApiResource
{
    public const ?string OBJECT_NAME = 'prospect_activity';
}
