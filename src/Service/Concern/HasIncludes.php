<?php

declare(strict_types=1);

namespace Enlivy\Service\Concern;

use Enlivy\Exception\InvalidArgumentException;

trait HasIncludes
{
    /**
     * Validate the 'include' parameter against AVAILABLE_INCLUDES.
     *
     * Accepts both array (['a', 'b']) and comma-separated string ('a,b') formats.
     * Arrays are normalized to comma-separated strings for the API.
     *
     * @throws InvalidArgumentException If any requested include is not in AVAILABLE_INCLUDES.
     */
    protected function validateIncludes(array &$params): void
    {
        if (!isset($params['include'])) {
            return;
        }

        $availableIncludes = static::AVAILABLE_INCLUDES;

        $requested = is_array($params['include'])
            ? $params['include']
            : array_map('trim', explode(',', $params['include']));

        $requested = array_filter($requested, fn (string $v): bool => $v !== '');

        $invalid = array_diff($requested, $availableIncludes);

        if (count($invalid) > 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid include(s): %s. Available includes for this resource: %s',
                    implode(', ', $invalid),
                    implode(', ', $availableIncludes),
                ),
            );
        }

        // Normalize array format to comma-separated string for the API.
        if (is_array($params['include'])) {
            $params['include'] = implode(',', $requested);
        }
    }
}
