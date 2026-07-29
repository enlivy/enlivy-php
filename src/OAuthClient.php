<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an OAuth Client in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $user_id
 * @property string|null $slug
 * @property array|null $name_lang_map
 * @property array|null $description_lang_map
 * @property array|null $redirect_uris
 * @property array|null $allowed_scopes
 * @property string|null $logo_file_extension
 * @property string|null $logo_url
 * @property string|null $homepage_url
 * @property string|null $privacy_policy_url
 * @property bool $is_first_party
 * @property bool $is_active
 * @property bool $is_verified
 * @property string $created_at
 * @property string $updated_at
 */
class OAuthClient extends ApiResource
{
    public const ?string OBJECT_NAME = 'oauth_client';
}
