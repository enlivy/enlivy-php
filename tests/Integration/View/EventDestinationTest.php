<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\EventDestination;
use Enlivy\Organization\Tag;
use Enlivy\Tests\Integration\IntegrationTestCase;

class EventDestinationTest extends IntegrationTestCase
{
    // Event Destinations

    public function testListEventDestinations(): void
    {
        $destinations = $this->getClient()->eventDestinations->list();

        $this->assertInstanceOf(Collection::class, $destinations);
        $this->assertIsArray($destinations->data);

        if (count($destinations->data) > 0) {
            $destination = $destinations->data[0];
            $this->assertInstanceOf(EventDestination::class, $destination);
            $this->assertNotNull($destination->id);
        }
    }

    public function testRetrieveEventDestination(): void
    {
        $destinations = $this->getClient()->eventDestinations->list(['per_page' => 1]);

        if (count($destinations->data) === 0) {
            $this->markTestSkipped('No event destinations available for testing');
        }

        $destinationId = $destinations->data[0]->id;
        $destination = $this->getClient()->eventDestinations->retrieve($destinationId);

        $this->assertInstanceOf(EventDestination::class, $destination);
        $this->assertEquals($destinationId, $destination->id);
    }

    // Tags

    public function testListTags(): void
    {
        $tags = $this->getClient()->tags->list();

        $this->assertInstanceOf(Collection::class, $tags);
        $this->assertIsArray($tags->data);

        if (count($tags->data) > 0) {
            $tag = $tags->data[0];
            $this->assertInstanceOf(Tag::class, $tag);
            $this->assertNotNull($tag->id);
        }
    }

    public function testRetrieveTag(): void
    {
        $tags = $this->getClient()->tags->list(['per_page' => 1]);

        if (count($tags->data) === 0) {
            $this->markTestSkipped('No tags available for testing');
        }

        $tagId = $tags->data[0]->id;
        $tag = $this->getClient()->tags->retrieve($tagId);

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals($tagId, $tag->id);
    }
}
