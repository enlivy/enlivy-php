<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyObject;
use PHPUnit\Framework\TestCase;

final class EnlivyObjectTest extends TestCase
{
    public function testConstructFromArray(): void
    {
        $obj = EnlivyObject::constructFrom([
            'id' => 'org_pros_xxx',
            'name' => 'Test Prospect',
            'email' => 'test@example.com',
        ]);

        $this->assertSame('org_pros_xxx', $obj->id);
        $this->assertSame('Test Prospect', $obj->name);
        $this->assertSame('test@example.com', $obj->email);
    }

    public function testNestedObjectsAreConvertedToEnlivyObjects(): void
    {
        $obj = EnlivyObject::constructFrom([
            'id' => 'org_pros_xxx',
            'contact' => [
                'name' => 'John Doe',
                'phone' => '+1234567890',
            ],
        ]);

        $this->assertInstanceOf(EnlivyObject::class, $obj->contact);
        $this->assertSame('John Doe', $obj->contact->name);
        $this->assertSame('+1234567890', $obj->contact->phone);
    }

    public function testArraysOfObjectsAreConvertedToEnlivyObjects(): void
    {
        $obj = EnlivyObject::constructFrom([
            'id' => 'org_inv_xxx',
            'line_items' => [
                ['id' => 'line_1', 'description' => 'Item 1'],
                ['id' => 'line_2', 'description' => 'Item 2'],
            ],
        ]);

        $this->assertIsArray($obj->line_items);
        $this->assertCount(2, $obj->line_items);
        $this->assertInstanceOf(EnlivyObject::class, $obj->line_items[0]);
        $this->assertSame('line_1', $obj->line_items[0]->id);
    }

    public function testMagicSetTracksUnsavedValues(): void
    {
        $obj = EnlivyObject::constructFrom(['id' => 'test']);

        $obj->name = 'Updated Name';
        $obj->email = 'updated@example.com';

        $unsaved = $obj->serializeUnsavedValues();

        $this->assertArrayHasKey('name', $unsaved);
        $this->assertArrayHasKey('email', $unsaved);
        $this->assertArrayNotHasKey('id', $unsaved);
    }

    public function testArrayAccessWorks(): void
    {
        $obj = EnlivyObject::constructFrom(['id' => 'test', 'name' => 'Test']);

        $this->assertSame('test', $obj['id']);
        $this->assertSame('Test', $obj['name']);
        $this->assertTrue(isset($obj['id']));
        $this->assertFalse(isset($obj['nonexistent']));

        $obj['description'] = 'New description';
        $this->assertSame('New description', $obj['description']);

        unset($obj['description']);
        $this->assertNull($obj['description']);
    }

    public function testToArrayRecursivelyConvertsObjects(): void
    {
        $obj = EnlivyObject::constructFrom([
            'id' => 'test',
            'nested' => [
                'key' => 'value',
            ],
        ]);

        $array = $obj->toArray();

        $this->assertIsArray($array);
        $this->assertSame('test', $array['id']);
        $this->assertIsArray($array['nested']);
        $this->assertSame('value', $array['nested']['key']);
    }

    public function testJsonSerialize(): void
    {
        $obj = EnlivyObject::constructFrom([
            'id' => 'test',
            'name' => 'Test',
        ]);

        $json = json_encode($obj);

        $this->assertSame('{"id":"test","name":"Test"}', $json);
    }

    public function testNullPropertyAccessReturnsNull(): void
    {
        $obj = EnlivyObject::constructFrom(['id' => 'test']);

        $this->assertNull($obj->nonexistent);
    }
}
