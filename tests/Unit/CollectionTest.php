<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testConstructFromApiResponse(): void
    {
        $collection = Collection::constructFrom([
            'data' => [
                ['id' => 'org_pros_1', 'name' => 'Prospect 1'],
                ['id' => 'org_pros_2', 'name' => 'Prospect 2'],
            ],
            'meta' => [
                'pagination' => [
                    'total' => 50,
                    'count' => 2,
                    'per_page' => 25,
                    'current_page' => 1,
                    'total_pages' => 2,
                ],
            ],
        ]);

        $this->assertCount(2, $collection);
        $this->assertSame(50, $collection->getTotalCount());
        $this->assertSame(1, $collection->getCurrentPage());
        $this->assertSame(25, $collection->getPerPage());
        $this->assertSame(2, $collection->getTotalPages());
        $this->assertTrue($collection->hasMore());
    }

    public function testGetDataReturnsEnlivyObjects(): void
    {
        $collection = Collection::constructFrom([
            'data' => [
                ['id' => 'org_pros_1'],
                ['id' => 'org_pros_2'],
            ],
        ]);

        $data = $collection->getData();

        $this->assertCount(2, $data);
        $this->assertInstanceOf(EnlivyObject::class, $data[0]);
        $this->assertSame('org_pros_1', $data[0]->id);
    }

    public function testIsEmptyWorks(): void
    {
        $empty = Collection::constructFrom(['data' => []]);
        $notEmpty = Collection::constructFrom(['data' => [['id' => 'test']]]);

        $this->assertTrue($empty->isEmpty());
        $this->assertFalse($notEmpty->isEmpty());
    }

    public function testIteratorAggregateWorks(): void
    {
        $collection = Collection::constructFrom([
            'data' => [
                ['id' => 'org_pros_1'],
                ['id' => 'org_pros_2'],
            ],
        ]);

        $ids = [];
        foreach ($collection as $item) {
            $ids[] = $item->id;
        }

        $this->assertSame(['org_pros_1', 'org_pros_2'], $ids);
    }

    public function testCountableWorks(): void
    {
        $collection = Collection::constructFrom([
            'data' => [
                ['id' => '1'],
                ['id' => '2'],
                ['id' => '3'],
            ],
        ]);

        $this->assertSame(3, count($collection));
    }

    public function testHasMoreOnLastPage(): void
    {
        $collection = Collection::constructFrom([
            'data' => [['id' => 'test']],
            'meta' => [
                'pagination' => [
                    'current_page' => 3,
                    'total_pages' => 3,
                ],
            ],
        ]);

        $this->assertFalse($collection->hasMore());
    }

    public function testNoPaginationMetaDefaults(): void
    {
        $collection = Collection::constructFrom([
            'data' => [['id' => 'test']],
        ]);

        $this->assertNull($collection->getPagination());
        $this->assertFalse($collection->hasMore());
        $this->assertSame(1, $collection->getTotalCount());
        $this->assertSame(1, $collection->getCurrentPage());
        $this->assertSame(1, $collection->getTotalPages());
    }
}
