<?php

declare(strict_types=1);

namespace Enlivy;

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
