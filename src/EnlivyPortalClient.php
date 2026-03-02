<?php

declare(strict_types=1);

namespace Enlivy;

use Enlivy\Auth\ClientPortalAuth;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\HttpClient\CurlClient;
use Enlivy\HttpClient\HttpClientInterface;
use Enlivy\Service\ClientPortal\PortalServiceFactory;

/**
 * Client for the Enlivy Customer Portal API.
 *
 * Authenticates using a client portal session token and provides
 * access to portal-specific endpoints.
 *
 * Usage:
 *   $portal = new EnlivyPortalClient([
 *       'portal_token' => 'session-uuid-token',
 *       'organization_id' => 'org_xxx',
 *   ]);
 *   $invoices = $portal->invoices->list();
 *
 * @property Service\ClientPortal\ProfileService $profile
 * @property Service\ClientPortal\InvoiceService $invoices
 * @property Service\ClientPortal\ReceiptService $receipts
 * @property Service\ClientPortal\ContractService $contracts
 * @property Service\ClientPortal\NetworkExchangeService $networkExchanges
 * @property Service\ClientPortal\ReportService $reports
 * @property Service\ClientPortal\ReportSchemaService $reportSchemas
 * @property Service\ClientPortal\GuidelineService $guidelines
 * @property Service\ClientPortal\PlaybookService $playbooks
 * @property Service\ClientPortal\OfferService $offers
 * @property Service\ClientPortal\ProposalService $proposals
 * @property Service\ClientPortal\ProjectService $projects
 * @property Service\ClientPortal\ProspectService $prospects
 */
class EnlivyPortalClient implements EnlivyClientInterface
{
    private readonly string $apiBase;
    private readonly ?string $organizationId;
    private readonly ApiRequestor $requestor;
    private readonly PortalServiceFactory $serviceFactory;

    /**
     * @param array{
     *     portal_token?: string,
     *     organization_id?: string,
     *     api_base?: string,
     *     http_client?: HttpClientInterface,
     *     max_retries?: int,
     *     timeout?: int,
     * } $config
     */
    public function __construct(array $config = [])
    {
        $portalToken = $config['portal_token'] ?? Enlivy::getPortalToken();

        if (empty($portalToken)) {
            throw new InvalidArgumentException(
                'You must provide "portal_token" in the client configuration or via Enlivy::setPortalToken().',
            );
        }

        $this->apiBase = rtrim($config['api_base'] ?? Enlivy::getApiBase(), '/');
        $this->organizationId = $config['organization_id'] ?? Enlivy::getOrganizationId();

        $httpClient = $config['http_client'] ?? new CurlClient();
        $authHandler = new ClientPortalAuth($portalToken);

        $this->requestor = new ApiRequestor(
            authHandler: $authHandler,
            httpClient: $httpClient,
            apiBase: $this->apiBase,
            maxRetries: $config['max_retries'] ?? Enlivy::getMaxNetworkRetries(),
            timeout: $config['timeout'] ?? Enlivy::getTimeout(),
        );

        $this->serviceFactory = new PortalServiceFactory($this);
    }

    public function __get(string $name): mixed
    {
        return $this->serviceFactory->getService($name);
    }

    public function getRequestor(): ApiRequestor
    {
        return $this->requestor;
    }

    public function getOrganizationId(): ?string
    {
        return $this->organizationId;
    }

    public function getApiBase(): string
    {
        return $this->apiBase;
    }
}
