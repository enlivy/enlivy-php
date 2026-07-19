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
}
