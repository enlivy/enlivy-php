<?php

declare(strict_types=1);

namespace Enlivy\Embed;

use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Organization\CheckoutSession;

final class CheckoutEmbed
{
    public const string SURFACE = 'checkout';

    private const int JSON_FLAGS = JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

    /**
     * @param array{
     *     theme?: 'light'|'dark'|'auto',
     *     locale?: string,
     *     paymentMethodKind?: 'card'|'bank_transfer',
     *     confirmLabel?: string,
     * } $options
     */
    public function __construct(
        public readonly string $portalBaseUrl,
        public readonly string $clientToken,
        public readonly string $billingPackageId,
        public readonly array $options = [],
        public readonly string $containerId = 'enlivy-checkout',
    ) {
        $scheme = parse_url($portalBaseUrl, PHP_URL_SCHEME);
        if (! in_array($scheme, ['https', 'http'], true) || parse_url($portalBaseUrl, PHP_URL_HOST) === null) {
            throw new InvalidArgumentException('The portal base URL must be an absolute http(s) URL.');
        }
        if ($clientToken === '') {
            throw new InvalidArgumentException('The client token is empty.');
        }
        if (preg_match('/^[A-Za-z][\w-]*$/', $containerId) !== 1) {
            throw new InvalidArgumentException('The container id must be a plain HTML id.');
        }
    }

    /**
     * Only the response of `checkoutSessions->create()` (or the portal's `openCheckoutSession()`) carries
     * the token; a retrieved or listed session never does.
     *
     * @param array<string, string> $options
     */
    public static function fromSession(CheckoutSession $session, string $portalBaseUrl, array $options = []): self
    {
        $token = $session->lastResponse()?->json['meta']['client_token'] ?? null;
        if (! is_string($token) || $token === '') {
            throw new InvalidArgumentException('This session carries no client token: only the response that opened it does, and a closed session answers none.');
        }

        return new self($portalBaseUrl, $token, (string) $session->organization_billing_package_id, $options);
    }

    /**
     * @return array<string, string>
     */
    public function config(): array
    {
        return [
            'surface' => self::SURFACE,
            'clientToken' => $this->clientToken,
            'package' => $this->billingPackageId,
            'container' => '#' . $this->containerId,
        ] + array_map('strval', $this->options);
    }

    public function loaderUrl(): string
    {
        return rtrim($this->portalBaseUrl, '/') . '/embed.js';
    }

    /**
     * The container, the queueing stub with this checkout's `init()`, and the loader. Pass the page's CSP
     * nonce when it has one. Listen for `enlivy-portal:confirm` on `document` to learn the payment was
     * taken, then confirm it on your server with `checkoutSessions->retrieve()`.
     */
    public function html(?string $nonce = null): string
    {
        $nonceAttribute = $nonce === null ? '' : ' nonce="' . htmlspecialchars($nonce, ENT_QUOTES) . '"';

        return '<div id="' . $this->containerId . '"></div>' . "\n"
            . '<script' . $nonceAttribute . '>'
            . 'window.EnlivyPortal=window.EnlivyPortal||{};'
            . 'EnlivyPortal._q=EnlivyPortal._q||[];'
            . 'EnlivyPortal.init=EnlivyPortal.init||function(c){this._q.push(c);};'
            . 'EnlivyPortal.init(' . json_encode($this->config(), self::JSON_FLAGS) . ');'
            . '</script>' . "\n"
            . '<script src="' . htmlspecialchars($this->loaderUrl(), ENT_QUOTES) . '"' . $nonceAttribute . ' async></script>';
    }
}
