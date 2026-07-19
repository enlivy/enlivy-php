<?php

declare(strict_types=1);

namespace Enlivy;

use Enlivy\Util\RequestOptions;

/**
 * Represents a paginated API response.
 *
 * API format:
 * {
 *   "data": [...],
 *   "meta": {
 *     "pagination": {
 *       "total": 100,
 *       "count": 25,
 *       "per_page": 25,
 *       "current_page": 1,
 *       "total_pages": 4,
 *       "links": { "next": "...", "previous": "..." }
 *     }
 *   }
 * }
 *
 * @template T of EnlivyObject
 */
class Collection extends EnlivyObject implements \Countable, \IteratorAggregate
{
    private ?ApiRequestor $requestor = null;

    private string $requestMethod = 'GET';

    private string $requestPath = '';

    private ?array $requestParams = null;

    private ?RequestOptions $requestOpts = null;

    /** @var class-string<EnlivyObject>|null */
    private ?string $requestItemClass = null;

    /**
     * Remember how this page was fetched so autoPagingIterator() can request
     * the following pages.
     *
     * @param class-string<EnlivyObject>|null $itemClass
     */
    public function setRequestContext(
        ApiRequestor $requestor,
        string $method,
        string $path,
        ?array $params,
        ?RequestOptions $opts,
        ?string $itemClass,
    ): void {
        $this->requestor = $requestor;
        $this->requestMethod = $method;
        $this->requestPath = $path;
        $this->requestParams = $params;
        $this->requestOpts = $opts;
        $this->requestItemClass = $itemClass;
    }

    /**
     * Iterate every item across all pages, fetching follow-up pages lazily.
     *
     * @return \Generator<int, T>
     */
    public function autoPagingIterator(): \Generator
    {
        $page = $this;

        while (true) {
            foreach ($page->getData() as $item) {
                yield $item;
            }

            if ($this->requestor === null || !$page->hasMore()) {
                return;
            }

            $params = $this->requestParams ?? [];
            $params['page'] = $page->getCurrentPage() + 1;

            /** @var Collection<T> $page */
            $page = $this->requestor->requestCollection(
                $this->requestMethod,
                $this->requestPath,
                $params,
                $this->requestOpts,
                $this->requestItemClass,
            );
        }
    }

    /**
     * Refresh collection from API data with typed items.
     *
     * @template TItem of EnlivyObject
     * @param array<string, mixed> $data The raw API response
     * @param class-string<TItem>|null $itemClass The class to use for items in the data array
     */
    public function refreshFromWithClass(array $data, ?string $itemClass = null): void
    {
        $this->values = [];
        $this->unsavedValues = new \Enlivy\Util\Set();

        foreach ($data as $key => $value) {
            if ($key === 'data' && is_array($value) && $itemClass !== null) {
                // Hydrate each item in data array with the specified class
                $this->values[$key] = array_map(
                    static function (mixed $item) use ($itemClass): EnlivyObject {
                        if (is_array($item)) {
                            $obj = new $itemClass($item['id'] ?? null);
                            $obj->refreshFrom($item);
                            return $obj;
                        }
                        return $item instanceof EnlivyObject ? $item : EnlivyObject::constructFrom((array) $item);
                    },
                    $value,
                );
            } else {
                $this->values[$key] = self::convertValueStatic($value);
            }
        }
    }

    /**
     * Convert a value for storage, using EnlivyObject for nested objects.
     */
    private static function convertValueStatic(mixed $value): mixed
    {
        if (is_array($value)) {
            if (\Enlivy\Util\Util::isAssociativeArray($value)) {
                return EnlivyObject::constructFrom($value);
            }

            return array_map(
                static fn(mixed $item): mixed => is_array($item) ? EnlivyObject::constructFrom($item) : $item,
                $value,
            );
        }

        return $value;
    }

    /**
     * Get the data items in this collection.
     *
     * @return list<T>
     */
    public function getData(): array
    {
        return $this->values['data'] ?? [];
    }

    public function getPagination(): ?EnlivyObject
    {
        /** @var EnlivyObject|null $meta */
        $meta = $this->values['meta'] ?? null;

        if ($meta === null) {
            return null;
        }

        /** @var EnlivyObject|null */
        return $meta['pagination'] ?? null;
    }

    public function hasMore(): bool
    {
        $pagination = $this->getPagination();

        if ($pagination === null) {
            return false;
        }

        /** @var int $currentPage */
        $currentPage = $pagination['current_page'] ?? 1;
        /** @var int $totalPages */
        $totalPages = $pagination['total_pages'] ?? 1;

        return $currentPage < $totalPages;
    }

    public function getTotalCount(): int
    {
        $pagination = $this->getPagination();

        return $pagination !== null ? (int) ($pagination['total'] ?? 0) : count($this->getData());
    }

    public function getCurrentPage(): int
    {
        $pagination = $this->getPagination();

        return $pagination !== null ? (int) ($pagination['current_page'] ?? 1) : 1;
    }

    public function getPerPage(): int
    {
        $pagination = $this->getPagination();

        return $pagination !== null ? (int) ($pagination['per_page'] ?? 25) : count($this->getData());
    }

    public function getTotalPages(): int
    {
        $pagination = $this->getPagination();

        return $pagination !== null ? (int) ($pagination['total_pages'] ?? 1) : 1;
    }

    public function count(): int
    {
        return count($this->getData());
    }

    /**
     * @return \ArrayIterator<int, T>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->getData());
    }

    /**
     * Check if the collection is empty.
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Get the first item in the collection, or null if empty.
     *
     * @return T|null
     */
    public function first(): ?EnlivyObject
    {
        $data = $this->getData();

        return $data[0] ?? null;
    }

    /**
     * Get the last item in the collection, or null if empty.
     *
     * @return T|null
     */
    public function last(): ?EnlivyObject
    {
        $data = $this->getData();

        if (empty($data)) {
            return null;
        }

        return $data[count($data) - 1];
    }
}
