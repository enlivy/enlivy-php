<?php

declare(strict_types=1);

namespace Enlivy\Enums\Concern;

/**
 * Value/name list helpers for the string-backed enums in this namespace.
 */
trait EnumValues
{
    /** @return list<string> */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }

    /** @return list<string> */
    public static function names(): array
    {
        return array_map(static fn (self $c): string => $c->name, self::cases());
    }

    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
