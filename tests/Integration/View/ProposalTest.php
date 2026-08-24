<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Proposal;
use Enlivy\Organization\BillingPackage;
use Enlivy\Organization\ProposalNotificationLog;
use Enlivy\Tests\Integration\IntegrationTestCase;

class ProposalTest extends IntegrationTestCase
{
    // Proposals

    public function testListProposals(): void
    {
        $proposals = $this->getClient()->proposals->list();

        $this->assertInstanceOf(Collection::class, $proposals);
        $this->assertIsArray($proposals->data);

        if (count($proposals->data) > 0) {
            $proposal = $proposals->data[0];
            $this->assertInstanceOf(Proposal::class, $proposal);
            $this->assertIdPrefix('org_prop_', $proposal->id);
            $this->assertNotNull($proposal->organization_id);
        }
    }

    public function testListProposalsWithPagination(): void
    {
        $proposals = $this->getClient()->proposals->list(['page' => 1]);

        $this->assertInstanceOf(Collection::class, $proposals);
        $this->assertNotNull($proposals->meta);
    }

    public function testRetrieveProposal(): void
    {
        $proposals = $this->getClient()->proposals->list(['per_page' => 1]);

        if (count($proposals->data) === 0) {
            $this->markTestSkipped('No proposals available for testing');
        }

        $proposalId = $proposals->data[0]->id;
        $proposal = $this->getClient()->proposals->retrieve($proposalId);

        $this->assertInstanceOf(Proposal::class, $proposal);
        $this->assertEquals($proposalId, $proposal->id);
    }

    public function testRetrieveProposalWithInclude(): void
    {
        $proposals = $this->getClient()->proposals->list(['per_page' => 1]);

        if (count($proposals->data) === 0) {
            $this->markTestSkipped('No proposals available for testing');
        }

        $proposal = $this->getClient()->proposals->retrieve(
            $proposals->data[0]->id,
            ['include' => 'billing_package,receiver_user']
        );

        $this->assertInstanceOf(Proposal::class, $proposal);
    }

    // Billing Packages

    public function testListBillingPackages(): void
    {
        $packages = $this->getClient()->billingPackages->list();

        $this->assertInstanceOf(Collection::class, $packages);
        $this->assertIsArray($packages->data);

        if (count($packages->data) > 0) {
            $package = $packages->data[0];
            $this->assertInstanceOf(BillingPackage::class, $package);
            $this->assertIdPrefix('org_bp_', $package->id);
            $this->assertNotNull($package->organization_id);
        }
    }

    public function testRetrieveBillingPackage(): void
    {
        $packages = $this->getClient()->billingPackages->list(['per_page' => 1]);

        if (count($packages->data) === 0) {
            $this->markTestSkipped('No billing packages available for testing');
        }

        $packageId = $packages->data[0]->id;
        $package = $this->getClient()->billingPackages->retrieve($packageId);

        $this->assertInstanceOf(BillingPackage::class, $package);
        $this->assertEquals($packageId, $package->id);
    }

    // Notification Logs

    public function testListProposalNotificationLogs(): void
    {
        $logs = $this->getClient()->proposalNotificationLogs->list();

        $this->assertInstanceOf(Collection::class, $logs);
        $this->assertIsArray($logs->data);

        if (count($logs->data) > 0) {
            $log = $logs->data[0];
            $this->assertInstanceOf(ProposalNotificationLog::class, $log);
            $this->assertNotNull($log->organization_proposal_id);
            $this->assertIsBool($log->is_seller_notification);
        }
    }
}
