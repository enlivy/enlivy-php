<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an Invitation Code in the Enlivy API.
 *
 * @property string $id
 * @property string $code
 * @property string $user_id
 * @property string $organization_id
 * @property int $uses
 * @property int $max_uses
 * @property string|null $expires_at
 * @property string $created_at
 * @property string $updated_at
 */
class InvitationCode extends ApiResource
{
    public const ?string OBJECT_NAME = 'invitation_code';
}
