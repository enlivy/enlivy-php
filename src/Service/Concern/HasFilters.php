<?php

declare(strict_types=1);

namespace Enlivy\Service\Concern;

use Enlivy\Exception\InvalidArgumentException;

trait HasFilters
{
    /**
     * Global filters accepted by all list endpoints.
     * These are handled by the API's AbstractRepository and do not need
     * to be declared in each service's AVAILABLE_FILTERS constant.
     */
    public const array GLOBAL_FILTERS = [
        'q',
        'q_in',
        'ids',
        'order_by',
        'order',
        'page',
        'per_page',
        'deleted',
        'tag_ids',
    ];

    /**
     * Parameter keys that are not filters and should be skipped during validation.
     */
    private const array BYPASS_KEYS = [
        'include',
        'organization_id',
    ];

    /**
     * Validate filter parameters against AVAILABLE_FILTERS.
     *
     * Allows all global filters (q, ids, page, per_page, etc.) automatically.
     * Only resource-specific filters need to be declared in AVAILABLE_FILTERS.
     *
     * @throws InvalidArgumentException If any filter parameter is not recognized.
     */
    protected function validateFilters(array $params): void
    {
        $availableFilters = static::AVAILABLE_FILTERS;

        $allowed = array_merge(
            self::GLOBAL_FILTERS,
            self::BYPASS_KEYS,
            $availableFilters,
        );

        $invalid = array_diff(array_keys($params), $allowed);

        if (count($invalid) > 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unknown filter(s): %s. Available filters for this resource: %s (plus global filters: %s)',
                    implode(', ', $invalid),
                    count($availableFilters) > 0
                        ? implode(', ', $availableFilters)
                        : '(none)',
                    implode(', ', self::GLOBAL_FILTERS),
                ),
            );
        }
    }
}
