<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * A developer's own App: its revisions (without their bundles), the OAuth clients that act for its
 * provider, and where it is installed among the organizations the developer still works in.
 *
 * @property string $id
 * @property string $object
 * @property string $key
 * @property array $name_lang_map
 * @property string $distribution
 * @property string|null $published_revision_id
 * @property string|null $disabled_at
 * @property array $revisions
 * @property array $oauth_clients
 * @property array $installations
 * @property string $created_at
 * @property string $updated_at
 */
class UserApp extends ApiResource
{
    public const ?string OBJECT_NAME = 'user_app';
}
