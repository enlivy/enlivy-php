<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * @property string $access_token
 * @property string $token_type
 * @property int $expires_in
 * @property string $refresh_token
 * @property string $scope
 */
class OAuthToken extends ApiResource
{
    public const ?string OBJECT_NAME = 'oauth_token';
}
