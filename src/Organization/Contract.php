<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Contract in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_contract_status_id
 * @property string|null $organization_contract_prefix_id
 * @property string|null $organization_sender_user_id
 * @property string|null $organization_receiver_user_id
 * @property string|null $organization_file_id
 * @property string|null $category
 * @property string $number
 * @property string $title
 * @property string|null $sub_title
 * @property string $direction
 * @property string $source
 * @property string $layout
 * @property string|null $content_introduction
 * @property string|null $content_signature_disclaimer
 * @property bool $is_signed_by_sender_user
 * @property bool $is_signed_by_receiver_user
 * @property string|null $issued_at
 * @property string|null $ends_at
 * @property string|null $renewed_at
 * @property string|null $sender_signed_at
 * @property string|null $receiver_signed_at
 * @property string|null $signed_by_all_parties_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class Contract extends ApiResource
{
    public const ?string OBJECT_NAME = 'contract';
}
