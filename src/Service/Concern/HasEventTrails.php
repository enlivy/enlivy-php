<?php

declare(strict_types=1);

namespace Enlivy\Service\Concern;

use Enlivy\Collection;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Organization\EventTrail;
use Enlivy\Util\RequestOptions;

/**
 * Read-only access to a resource's event trail (its audit history).
 *
 * Mixed into services whose endpoint exposes a nested `event-trails` collection.
 */
trait HasEventTrails
{
    public const array EVENT_TRAIL_INCLUDES = [
        'changes',
        'actor_organization_user',
        'charge_log',
    ];

    /**
     * List event-trail entries for this resource type.
     *
     * Filters: `subject_id`, `event_type`, `origin`, `occurred_from`, `occurred_to`.
     *
     * @return Collection<EventTrail>
     */
    public function eventTrails(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateEventTrailIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, static::RESOURCE . '/event-trails'), $params, $opts, EventTrail::class);
    }

    public function retrieveEventTrail(string $id, array $params = [], ?RequestOptions $opts = null): EventTrail
    {
        $this->validateEventTrailIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, static::RESOURCE . "/event-trails/{$id}"), $params, $opts, EventTrail::class);
    }

    private function validateEventTrailIncludes(array &$params): void
    {
        if (!isset($params['include'])) {
            return;
        }

        $requested = array_filter(
            is_array($params['include'])
                ? $params['include']
                : array_map('trim', explode(',', $params['include'])),
            static fn (string $v): bool => $v !== '',
        );

        $invalid = array_diff($requested, self::EVENT_TRAIL_INCLUDES);

        if (count($invalid) > 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid event-trail include(s): %s. Available: %s',
                    implode(', ', $invalid),
                    implode(', ', self::EVENT_TRAIL_INCLUDES),
                ),
            );
        }

        if (is_array($params['include'])) {
            $params['include'] = implode(',', $requested);
        }
    }
}
