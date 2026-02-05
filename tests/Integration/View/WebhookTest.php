<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Webhook;
use Enlivy\Organization\Tag;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Webhook and Tag-related endpoints.
 */
class WebhookTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Webhooks
    // -------------------------------------------------------------------------

    public function testListWebhooks(): void
    {
        $webhooks = $this->getClient()->webhooks->list();

        $this->assertInstanceOf(Collection::class, $webhooks);
        $this->assertIsArray($webhooks->data);

        if (count($webhooks->data) > 0) {
            $webhook = $webhooks->data[0];
            $this->assertInstanceOf(Webhook::class, $webhook);
            $this->assertNotNull($webhook->id);
        }
    }

    public function testRetrieveWebhook(): void
    {
        $webhooks = $this->getClient()->webhooks->list(['per_page' => 1]);

        if (count($webhooks->data) === 0) {
            $this->markTestSkipped('No webhooks available for testing');
        }

        $webhookId = $webhooks->data[0]->id;
        $webhook = $this->getClient()->webhooks->retrieve($webhookId);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertEquals($webhookId, $webhook->id);
    }

    // -------------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------------

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
