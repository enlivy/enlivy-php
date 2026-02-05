<?php

declare(strict_types=1);

namespace Enlivy\Util;

final class Set
{
    /** @var array<string, true> */
    private array $items = [];

    public function add(string $key): void
    {
        $this->items[$key] = true;
    }

    public function contains(string $key): bool
    {
        return isset($this->items[$key]);
    }

    public function remove(string $key): void
    {
        unset($this->items[$key]);
    }

    public function clear(): void
    {
        $this->items = [];
    }

    /** @return list<string> */
    public function toArray(): array
    {
        return array_keys($this->items);
    }
}
