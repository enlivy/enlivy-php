<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Proposal;
use Enlivy\Organization\Offer;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Proposal and Offer-related endpoints.
 */
class ProposalTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Proposals
    // -------------------------------------------------------------------------

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
            ['include' => 'offer,recipients']
        );

        $this->assertInstanceOf(Proposal::class, $proposal);
    }

    // -------------------------------------------------------------------------
    // Offers
    // -------------------------------------------------------------------------

    public function testListOffers(): void
    {
        $offers = $this->getClient()->offers->list();

        $this->assertInstanceOf(Collection::class, $offers);
        $this->assertIsArray($offers->data);

        if (count($offers->data) > 0) {
            $offer = $offers->data[0];
            $this->assertInstanceOf(Offer::class, $offer);
            $this->assertIdPrefix('org_offr_', $offer->id);
            $this->assertNotNull($offer->organization_id);
        }
    }

    public function testRetrieveOffer(): void
    {
        $offers = $this->getClient()->offers->list(['per_page' => 1]);

        if (count($offers->data) === 0) {
            $this->markTestSkipped('No offers available for testing');
        }

        $offerId = $offers->data[0]->id;
        $offer = $this->getClient()->offers->retrieve($offerId);

        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertEquals($offerId, $offer->id);
    }
}
