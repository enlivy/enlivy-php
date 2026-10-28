<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Answered by `appInstallations->create()`. Upgrading, changing permissions, suspending and removing
 * an installation, and its connections, are administered in the dashboard.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $user_app_id
 * @property array $app
 * @property string $user_app_revision_id
 * @property int $manifest_revision
 * @property string $manifest_version
 * @property int $generation
 * @property string $status
 * @property array $granted_permissions
 * @property array $declared_permissions
 * @property array $capability_labels
 * @property string|null $connect_url
 * @property array $adds
 * @property array|null $upgrade
 * @property string $approved_at
 * @property string|null $suspended_at
 * @property string|null $removed_at
 * @property string $created_at
 * @property string $updated_at
 */
class AppInstallation extends ApiResource
{
    public const ?string OBJECT_NAME = 'app_installation';
}
