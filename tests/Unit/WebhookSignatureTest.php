<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Webhook\WebhookSignature;
use PHPUnit\Framework\TestCase;

final class WebhookSignatureTest extends TestCase
{
    private const string SECRET = 'whsec_test_secret';

    public function testValidSignatureReturnsTrue(): void
    {
        $payload = '{"id":"evt_xxx","type":"invoice.created"}';
        $timestamp = time();
        $expectedSig = hash_hmac('sha256', "{$timestamp}.{$payload}", self::SECRET);
        $signature = "t={$timestamp},v1={$expectedSig}";

        $result = WebhookSignature::verify($payload, $signature, self::SECRET);

        $this->assertTrue($result);
    }

    public function testInvalidSignatureThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('verification failed');

        $payload = '{"id":"evt_xxx"}';
        $timestamp = time();
        $signature = "t={$timestamp},v1=invalid_signature";

        WebhookSignature::verify($payload, $signature, self::SECRET);
    }

    public function testExpiredTimestampThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('tolerance zone');

        $payload = '{"id":"evt_xxx"}';
        $timestamp = time() - 600; // 10 minutes ago
        $expectedSig = hash_hmac('sha256', "{$timestamp}.{$payload}", self::SECRET);
        $signature = "t={$timestamp},v1={$expectedSig}";

        WebhookSignature::verify($payload, $signature, self::SECRET, 300); // 5 min tolerance
    }

    public function testMissingTimestampThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('format');

        $signature = 'v1=some_signature';

        WebhookSignature::verify('{}', $signature, self::SECRET);
    }

    public function testMissingSignatureThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('format');

        $signature = 't=12345678';

        WebhookSignature::verify('{}', $signature, self::SECRET);
    }

    public function testMultipleSignaturesOneValidPasses(): void
    {
        $payload = '{"id":"evt_xxx"}';
        $timestamp = time();
        $validSig = hash_hmac('sha256', "{$timestamp}.{$payload}", self::SECRET);
        $signature = "t={$timestamp},v1=invalid_one,v1={$validSig}";

        $result = WebhookSignature::verify($payload, $signature, self::SECRET);

        $this->assertTrue($result);
    }

    public function testZeroToleranceSkipsTimestampCheck(): void
    {
        $payload = '{"id":"evt_xxx"}';
        $timestamp = time() - 86400; // 24 hours ago
        $expectedSig = hash_hmac('sha256', "{$timestamp}.{$payload}", self::SECRET);
        $signature = "t={$timestamp},v1={$expectedSig}";

        // With tolerance = 0, timestamp check is skipped
        $result = WebhookSignature::verify($payload, $signature, self::SECRET, 0);

        $this->assertTrue($result);
    }
}
