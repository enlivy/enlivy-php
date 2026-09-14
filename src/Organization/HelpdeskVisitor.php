<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_user_id
 * @property string|null $organization_prospect_id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $browser_language
 * @property string|null $browser_timezone
 * @property int $session_count
 * @property int $page_view_count
 * @property array|null $first_pages
 * @property array|null $last_pages
 * @property bool $is_identified
 * @property bool $is_blocked
 * @property string|null $identified_at
 * @property string|null $identity_verified_at
 * @property string|null $consent_recorded_at
 * @property string|null $blocked_at
 * @property string|null $first_seen_at
 * @property string|null $last_seen_at
 * @property string|null $token_expires_at
 * @property string $created_at
 * @property string $updated_at
 */
class HelpdeskVisitor extends ApiResource
{
    public const ?string OBJECT_NAME = 'helpdesk_visitor';
}
