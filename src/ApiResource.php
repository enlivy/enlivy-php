<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Base class for all Enlivy API resources.
 *
 * Provides typed object support with automatic hydration from API responses.
 */
abstract class ApiResource extends EnlivyObject
{
    /**
     * The object type name as returned by the API.
     * Override in subclasses to enable automatic type mapping.
     */
    public const ?string OBJECT_NAME = null;
}
