<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration;

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use PHPUnit\Framework\TestCase;

/**
 * Base class for integration tests that run against a real Enlivy API.
 *
 * Required environment variables:
 *   ENLIVY_API_KEY        - API key (e.g., "1|your_token")
 *   ENLIVY_ORGANIZATION_ID - Organization ID (e.g., "org_xxx")
 *
 * Optional environment variables:
 *   ENLIVY_API_BASE       - API base URL (default: https://api.enlivy.com)
 *
 * To run integration tests:
 *   ENLIVY_API_KEY="1|xxx" ENLIVY_ORGANIZATION_ID="org_xxx" ./vendor/bin/phpunit tests/Integration
 *
 * For local development:
 *   ENLIVY_API_KEY="1|xxx" ENLIVY_ORGANIZATION_ID="org_xxx" ENLIVY_API_BASE="http://enlivy_api.test" ./vendor/bin/phpunit tests/Integration
 */
abstract class IntegrationTestCase extends TestCase
{
    protected static ?EnlivyClient $client = null;

    /**
     * Get or create the shared client instance.
     */
    protected function getClient(): EnlivyClient
    {
        if (self::$client === null) {
            self::$client = $this->createClient();
        }

        return self::$client;
    }

    /**
     * Create a fresh client instance (for tests that need isolation).
     */
    protected function createClient(): EnlivyClient
    {
        return new EnlivyClient([
            'api_key' => $this->getRequiredEnv('ENLIVY_API_KEY'),
            'organization_id' => $this->getRequiredEnv('ENLIVY_ORGANIZATION_ID'),
            'api_base' => $this->getEnv('ENLIVY_API_BASE', Enlivy::DEFAULT_API_BASE),
        ]);
    }

    /**
     * Get a required environment variable or skip the test.
     */
    protected function getRequiredEnv(string $name): string
    {
        $value = getenv($name);

        if ($value === false || $value === '') {
            $this->markTestSkipped(
                "Environment variable {$name} is required for integration tests. " .
                "Run with: {$name}=\"value\" ./vendor/bin/phpunit tests/Integration"
            );
        }

        return $value;
    }

    /**
     * Get an optional environment variable with a default.
     */
    protected function getEnv(string $name, string $default = ''): string
    {
        $value = getenv($name);

        return ($value !== false && $value !== '') ? $value : $default;
    }

    /**
     * Get the organization ID from environment.
     */
    protected function getOrganizationId(): string
    {
        return $this->getRequiredEnv('ENLIVY_ORGANIZATION_ID');
    }

    /**
     * Assert that a response has the expected object type.
     */
    protected function assertObjectType(string $expected, object $object): void
    {
        $this->assertObjectHasProperty('object', $object);
        $this->assertEquals($expected, $object->object);
    }

    /**
     * Assert that an ID has the expected prefix.
     */
    protected function assertIdPrefix(string $prefix, string $id): void
    {
        $this->assertStringStartsWith(
            $prefix,
            $id,
            "Expected ID to start with '{$prefix}', got '{$id}'"
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Reset global state before each test
        Enlivy::reset();
    }
}
