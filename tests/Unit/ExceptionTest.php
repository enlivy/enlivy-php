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
}
