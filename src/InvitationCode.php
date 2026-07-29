<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an Invitation Code in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string|null $user_account_type_id
 * @property string $code
 * @property string $user_id
 * @property int|null $remaining_redeems
 * @property int|null $redeem_count
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class InvitationCode extends ApiResource
{
    public const ?string OBJECT_NAME = 'invitation_code';
}
