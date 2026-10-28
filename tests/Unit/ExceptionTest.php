<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\Exception\ApiException;
use Enlivy\Exception\AuthenticationException;
use Enlivy\Exception\ForbiddenException;
use Enlivy\Exception\NotFoundException;
use Enlivy\Exception\RateLimitException;
use Enlivy\Exception\ServerException;
use Enlivy\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

final class ExceptionTest extends TestCase
{
    public function testFactoryReturns401AsAuthenticationException(): void
    {
        $exception = ApiException::factory(401, ['message' => 'Unauthenticated'], []);

        $this->assertInstanceOf(AuthenticationException::class, $exception);
        $this->assertSame(401, $exception->getStatusCode());
    }

    public function testFactoryReturns403AsForbiddenException(): void
    {
        $exception = ApiException::factory(403, ['message' => 'Forbidden'], []);

        $this->assertInstanceOf(ForbiddenException::class, $exception);
    }

    public function testFactoryReturns404AsNotFoundException(): void
    {
        $exception = ApiException::factory(404, ['message' => 'Not found'], []);

        $this->assertInstanceOf(NotFoundException::class, $exception);
    }

    public function testFactoryReturns422AsValidationException(): void
    {
        $exception = ApiException::factory(422, [
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => ['The email field is required.'],
                'name' => ['The name must be a string.'],
            ],
        ], []);

        $this->assertInstanceOf(ValidationException::class, $exception);
        $this->assertSame(422, $exception->getStatusCode());

        $errors = $exception->errors();
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('name', $errors);
    }

    public function testFactoryReturns429AsRateLimitException(): void
    {
        $exception = ApiException::factory(429, ['message' => 'Too many requests'], [
            'Retry-After' => '60',
        ]);

        $this->assertInstanceOf(RateLimitException::class, $exception);
        $this->assertSame(60, $exception->retryAfter());
    }

    public function testFactoryReturns500AsServerException(): void
    {
        $exception = ApiException::factory(500, ['message' => 'Internal error'], []);

        $this->assertInstanceOf(ServerException::class, $exception);
    }

    public function testFactoryReturns502AsServerException(): void
    {
        $exception = ApiException::factory(502, [], []);

        $this->assertInstanceOf(ServerException::class, $exception);
    }

    public function testExceptionContainsBody(): void
    {
        $body = ['message' => 'Test error', 'code' => 'test_code'];
        $exception = ApiException::factory(400, $body, []);

        $this->assertSame($body, $exception->getBody());
    }

    public function testExceptionContainsHeaders(): void
    {
        $headers = ['X-Request-Id' => 'req_123'];
        $exception = ApiException::factory(400, [], $headers);

        $this->assertSame($headers, $exception->getHeaders());
    }

    public function testValidationExceptionReturnsEmptyArrayForNoErrors(): void
    {
        $exception = new ValidationException(
            message: 'Validation failed',
            statusCode: 422,
            body: ['message' => 'Validation failed'],
            headers: [],
        );

        $this->assertSame([], $exception->errors());
    }

    public function testACodedRefusalCarriesItsCodeAndFacts(): void
    {
        $exception = ApiException::factory(403, [
            'message' => 'This token does not reach that organization.',
            'error' => [
                'code' => 'route_denied',
                'denied_by' => 'scope',
                'required_abilities' => ['invoices.view'],
                'required_scopes' => ['accounting:read'],
            ],
            'meta' => ['denied_by' => 'scope'],
        ], []);

        $this->assertInstanceOf(ForbiddenException::class, $exception);
        $this->assertSame('route_denied', $exception->getRefusalCode());
        $this->assertSame([
            'denied_by' => 'scope',
            'required_abilities' => ['invoices.view'],
            'required_scopes' => ['accounting:read'],
        ], $exception->getFacts());
        $this->assertFalse($exception->isConflict());
    }

    public function testA409WithoutACodeIsAConflictToRereadAndRetry(): void
    {
        $exception = ApiException::factory(409, ['message' => 'The layout changed since you read it.'], []);

        $this->assertSame(ApiException::class, $exception::class);
        $this->assertTrue($exception->isConflict());
        $this->assertNull($exception->getRefusalCode());
        $this->assertSame([], $exception->getFacts());
    }

    public function testA409WithACodeNeedsSomethingElseFirst(): void
    {
        $exception = ApiException::factory(409, [
            'message' => 'Connect an email account before sending.',
            'error' => ['code' => 'email_sending_not_connected', 'can_connect' => true],
        ], []);

        $this->assertFalse($exception->isConflict());
        $this->assertSame('email_sending_not_connected', $exception->getRefusalCode());
        $this->assertSame(['can_connect' => true], $exception->getFacts());
    }

    public function testATopLevelCodeOrAnOAuthErrorStringIsNotARefusalCode(): void
    {
        $this->assertNull(ApiException::factory(400, ['message' => 'Bad request', 'code' => 400], [])->getRefusalCode());

        $oauth = ApiException::factory(400, ['error' => 'authorization_pending', 'error_description' => 'Waiting'], []);
        $this->assertNull($oauth->getRefusalCode());
        $this->assertSame([], $oauth->getFacts());

        $this->assertNull(ApiException::factory(502, null, [])->getRefusalCode());
        $this->assertFalse(ApiException::factory(502, null, [])->isConflict());
    }

    public function testEntitlementRefusalsKeepTheirCodeOnTheBaseException(): void
    {
        $exception = ApiException::factory(402, [
            'message' => 'The Reports pack is needed.',
            'error' => ['code' => 'tenant_feature_required', 'required_pack' => 'reports'],
        ], []);

        $this->assertSame(ApiException::class, $exception::class);
        $this->assertSame(402, $exception->getStatusCode());
        $this->assertSame('tenant_feature_required', $exception->getRefusalCode());
        $this->assertSame(['required_pack' => 'reports'], $exception->getFacts());
    }
}
