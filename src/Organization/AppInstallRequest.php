<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * A developer's request that an organization install their personal App, answered by
 * `appInstallRequests->create()` and listed by `userApps->installRequests()`. An admin approves or
 * declines it in the dashboard; `organization_app_installation_id` is set once it is installed.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $user_app_id
 * @property array $app
 * @property string $user_app_revision_id
 * @property int $manifest_revision
 * @property string $manifest_version
 * @property string $status
 * @property string $requested_by_user_id
 * @property string|null $requested_by_name
 * @property array $declared_permissions
 * @property array $capability_labels
 * @property string|null $decided_by_user_id
 * @property string|null $decided_at
 * @property string|null $organization_app_installation_id
 * @property string $created_at
 * @property string $updated_at
 */
class AppInstallRequest extends ApiResource
{
    public const ?string OBJECT_NAME = 'app_install_request';
}
