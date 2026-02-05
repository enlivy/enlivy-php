<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Contract;
use Enlivy\Organization\ContractPrefix;
use Enlivy\Organization\ContractStatus;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Contract-related endpoints.
 */
class ContractTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Contracts
    // -------------------------------------------------------------------------

    public function testListContracts(): void
    {
        $contracts = $this->getClient()->contracts->list();

        $this->assertInstanceOf(Collection::class, $contracts);
        $this->assertIsArray($contracts->data);

        if (count($contracts->data) > 0) {
            $contract = $contracts->data[0];
            $this->assertInstanceOf(Contract::class, $contract);
            $this->assertIdPrefix('org_cont_', $contract->id);
            $this->assertNotNull($contract->organization_id);
        }
    }

    public function testListContractsWithPagination(): void
    {
        $contracts = $this->getClient()->contracts->list(['page' => 1]);

        $this->assertInstanceOf(Collection::class, $contracts);
        $this->assertNotNull($contracts->meta);
    }

    public function testListContractsWithInclude(): void
    {
        $contracts = $this->getClient()->contracts->list([
            'include' => 'receiver_user,contract_parties',
        ]);

        $this->assertInstanceOf(Collection::class, $contracts);
    }

    public function testRetrieveContract(): void
    {
        $contracts = $this->getClient()->contracts->list(['per_page' => 1]);

        if (count($contracts->data) === 0) {
            $this->markTestSkipped('No contracts available for testing');
        }

        $contractId = $contracts->data[0]->id;
        $contract = $this->getClient()->contracts->retrieve($contractId);

        $this->assertInstanceOf(Contract::class, $contract);
        $this->assertEquals($contractId, $contract->id);
    }

    // -------------------------------------------------------------------------
    // Contract Prefixes
    // -------------------------------------------------------------------------

    public function testListContractPrefixes(): void
    {
        $prefixes = $this->getClient()->contractPrefixes->list();

        $this->assertInstanceOf(Collection::class, $prefixes);
        $this->assertIsArray($prefixes->data);

        if (count($prefixes->data) > 0) {
            $prefix = $prefixes->data[0];
            $this->assertInstanceOf(ContractPrefix::class, $prefix);
        }
    }

    // -------------------------------------------------------------------------
    // Contract Statuses
    // -------------------------------------------------------------------------

    public function testListContractStatuses(): void
    {
        $statuses = $this->getClient()->contractStatuses->list();

        $this->assertInstanceOf(Collection::class, $statuses);
        $this->assertIsArray($statuses->data);

        if (count($statuses->data) > 0) {
            $status = $statuses->data[0];
            $this->assertInstanceOf(ContractStatus::class, $status);
        }
    }
}
