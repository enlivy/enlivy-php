<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\Embed\CheckoutEmbed;
use Enlivy\EnlivyClient;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

final class CheckoutEmbedTest extends TestCase
{
    private MockHttpClient $httpClient;
    private EnlivyClient $client;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
        $this->client = new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
        ]);
    }

    public function testItIsBuiltFromTheResponseThatOpenedTheSession(): void
    {
        $this->httpClient->addResponse(201, [
            'data' => ['id' => 'org_chk_sess_1', 'organization_billing_package_id' => 'org_bp_1', 'status' => 'open'],
            'meta' => ['client_token' => 'tok_once'],
        ]);
        $session = $this->client->checkoutSessions->create(['organization_billing_package_id' => 'org_bp_1']);

        $embed = CheckoutEmbed::fromSession($session, 'https://acme.example.com/', ['theme' => 'dark', 'locale' => 'ro']);

        $this->assertSame([
            'surface' => 'checkout',
            'clientToken' => 'tok_once',
            'package' => 'org_bp_1',
            'container' => '#enlivy-checkout',
            'theme' => 'dark',
            'locale' => 'ro',
        ], $embed->config());
        $this->assertSame('https://acme.example.com/embed.js', $embed->loaderUrl());
    }

    public function testARetrievedSessionCarriesNoToken(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => ['id' => 'org_chk_sess_1', 'organization_billing_package_id' => 'org_bp_1', 'status' => 'open'],
        ]);
        $session = $this->client->checkoutSessions->retrieve('org_chk_sess_1');

        $this->expectException(InvalidArgumentException::class);
        CheckoutEmbed::fromSession($session, 'https://acme.example.com');
    }

    public function testTheSnippetCannotBeBrokenOutOfByItsValues(): void
    {
        $embed = new CheckoutEmbed('https://acme.example.com', 'tok</script><script>alert(1)</script>', 'org_bp_1', ['confirmLabel' => "Pay & 'go'"]);

        $html = $embed->html('n0nce"');

        $this->assertStringNotContainsString('</script><script>alert', $html);
        $this->assertStringContainsString('tok\\u003C/script\\u003E', $html);
        $this->assertStringContainsString('Pay \\u0026 \\u0027go\\u0027', $html);
        $this->assertSame(2, substr_count($html, 'nonce="n0nce&quot;"'));
        $this->assertStringContainsString('<script src="https://acme.example.com/embed.js" nonce="n0nce&quot;" async></script>', $html);
        $this->assertStringStartsWith('<div id="enlivy-checkout"></div>', $html);
    }

    public function testItRefusesWhatWouldNotLoad(): void
    {
        foreach ([
            fn () => new CheckoutEmbed('acme.example.com', 'tok', 'org_bp_1'),
            fn () => new CheckoutEmbed('javascript:alert(1)', 'tok', 'org_bp_1'),
            fn () => new CheckoutEmbed('https://acme.example.com', '', 'org_bp_1'),
            fn () => new CheckoutEmbed('https://acme.example.com', 'tok', 'org_bp_1', [], 'x" onload="y'),
        ] as $build) {
            try {
                $build();
                $this->fail('Expected the embed to be refused.');
            } catch (InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }
}
