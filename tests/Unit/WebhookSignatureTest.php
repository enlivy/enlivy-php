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
        $signature = hash_hmac('sha256', $payload, self::SECRET);

        $result = WebhookSignature::verify($payload, $signature, self::SECRET);

        $this->assertTrue($result);
    }

    public function testInvalidSignatureThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('verification failed');

        $payload = '{"id":"evt_xxx"}';

        WebhookSignature::verify($payload, 'invalid_signature', self::SECRET);
    }

    public function testEmptySignatureThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('empty');

        WebhookSignature::verify('{}', '', self::SECRET);
    }

    public function testWrongSecretFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('verification failed');

        $payload = '{"id":"evt_xxx"}';
        $signature = hash_hmac('sha256', $payload, self::SECRET);

        WebhookSignature::verify($payload, $signature, 'wrong_secret');
    }

    public function testTamperedPayloadFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('verification failed');

        $payload = '{"id":"evt_xxx"}';
        $signature = hash_hmac('sha256', $payload, self::SECRET);

        WebhookSignature::verify('{"id":"evt_tampered"}', $signature, self::SECRET);
    }

    public function testHeaderNameConstant(): void
    {
        $this->assertSame('Signature', WebhookSignature::HEADER_NAME);
    }
}
