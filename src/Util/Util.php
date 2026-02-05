<?php

declare(strict_types=1);

namespace Enlivy\Util;

final class Util
{
    /**
     * Determine if an array is associative (object-like) vs sequential (list-like).
     */
    public static function isAssociativeArray(array $array): bool
    {
        if ($array === []) {
            return false;
        }

        return !array_is_list($array);
    }

    /**
     * Encode parameters for a URL query string or form body.
     *
     * @return array<string, string>
     */
    public static function flattenParams(array $params, string $prefix = ''): array
    {
        $result = [];

        foreach ($params as $key => $value) {
            $fullKey = $prefix !== '' ? "{$prefix}[{$key}]" : (string) $key;

            if (is_array($value)) {
                $result = array_merge($result, self::flattenParams($value, $fullKey));
            } elseif ($value !== null) {
                $result[$fullKey] = match (true) {
                    is_bool($value) => $value ? '1' : '0',
                    default => (string) $value,
                };
            }
        }

        return $result;
    }
}
