<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\HttpClient\CurlClient;
use PHPUnit\Framework\TestCase;

/**
 * Exists because a dropped DELETE filter is silently destructive: `trashedItems->purge()`
 * narrows an irreversible purge with `entities`, and a param that never reaches the wire
 * would empty everything instead of the subset asked for.
 */
final class CurlClientQueryTest extends TestCase
{
    private function build(string $method, string $url, ?array $params): string
    {
        $reflection = new \ReflectionMethod(CurlClient::class, 'appendQuery');

        return (string) $reflection->invoke(null, $method, $url, $params);
    }

    public function testDeleteParamsTravelOnTheQueryString(): void
    {
        $url = $this->build('DELETE', 'https://api.enlivy.com/organizations/o/trashed-items', [
            'entities' => ['files', 'tags'],
        ]);

        $this->assertStringContainsString('entities%5B0%5D=files', $url);
        $this->assertStringContainsString('entities%5B1%5D=tags', $url);
    }

    public function testGetParamsStillTravelOnTheQueryString(): void
    {
        $url = $this->build('GET', 'https://api.enlivy.com/organizations/o/invoices', ['limit' => 10]);

        $this->assertSame('https://api.enlivy.com/organizations/o/invoices?limit=10', $url);
    }

    public function testEmptyParamsLeaveTheUrlUntouched(): void
    {
        $bare = 'https://api.enlivy.com/organizations/o/invoices';

        $this->assertSame($bare, $this->build('DELETE', $bare, []));
        $this->assertSame($bare, $this->build('GET', $bare, null));
    }

    public function testExistingQueryStringIsPreserved(): void
    {
        $url = $this->build('DELETE', 'https://api.enlivy.com/x?a=1', ['b' => 2]);

        $this->assertSame('https://api.enlivy.com/x?a=1&b=2', $url);
    }
}
