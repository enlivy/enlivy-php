<?php

declare(strict_types=1);

namespace Enlivy;

use Enlivy\Util\ObjectTypes;
use Enlivy\Util\Set;
use Enlivy\Util\Util;

class EnlivyObject implements \ArrayAccess, \JsonSerializable
{
    protected array $values = [];
    protected Set $unsavedValues;

    final public function __construct(?string $id = null)
    {
        $this->unsavedValues = new Set();

        if ($id !== null) {
            $this->values['id'] = $id;
        }
    }

    /**
     * Construct an object from API response data.
     *
     * If the data contains an 'object' field that maps to a known resource type,
     * the appropriate typed class will be instantiated instead of EnlivyObject.
     *
     * @return static|self
     */
    public static function constructFrom(array $data): self
    {
        // Check if we should use a typed resource class
        $objectType = $data['object'] ?? null;

        if ($objectType !== null) {
            $class = ObjectTypes::getClass($objectType);

            if ($class !== null) {
                $obj = new $class($data['id'] ?? null);
                $obj->refreshFrom($data);

                /** @var static */
                return $obj;
            }
        }

        $obj = new static($data['id'] ?? null);
        $obj->refreshFrom($data);

        return $obj;
    }

    public function refreshFrom(array $data): void
    {
        $this->values = [];
        $this->unsavedValues = new Set();

        foreach ($data as $key => $value) {
            $this->values[$key] = self::convertValue($value);
        }
    }

    private static function convertValue(mixed $value): mixed
    {
        if (is_array($value)) {
            if (Util::isAssociativeArray($value)) {
                // Always use EnlivyObject::constructFrom for nested values
                // to enable proper type mapping based on 'object' field
                return EnlivyObject::constructFrom($value);
            }

            return array_map(
                static fn(mixed $item): mixed => is_array($item) ? EnlivyObject::constructFrom($item) : $item,
                $value,
            );
        }

        return $value;
    }

    public function __get(string $name): mixed
    {
        return $this->values[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->values[$name] = $value;
        $this->unsavedValues->add($name);
    }

    public function __isset(string $name): bool
    {
        return isset($this->values[$name]);
    }

    public function __unset(string $name): void
    {
        unset($this->values[$name]);
        $this->unsavedValues->remove($name);
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->values as $key => $value) {
            $result[$key] = match (true) {
                $value instanceof self => $value->toArray(),
                is_array($value) => array_map(
                    static fn(mixed $item): mixed => $item instanceof self ? $item->toArray() : $item,
                    $value,
                ),
                default => $value,
            };
        }

        return $result;
    }

    /**
     * Get only the values that have been modified since construction.
     */
    public function serializeUnsavedValues(): array
    {
        $result = [];

        foreach ($this->unsavedValues->toArray() as $key) {
            if (isset($this->values[$key])) {
                $value = $this->values[$key];
                $result[$key] = $value instanceof self ? $value->toArray() : $value;
            }
        }

        return $result;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    // ArrayAccess

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->values[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->__set((string) $offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->__unset((string) $offset);
    }
}
