<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Receipt;
use Enlivy\Organization\ReceiptPrefix;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Receipt-related endpoints.
 */
class ReceiptTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Receipts
    // -------------------------------------------------------------------------

    public function testListReceipts(): void
    {
        $receipts = $this->getClient()->receipts->list();

        $this->assertInstanceOf(Collection::class, $receipts);
        $this->assertIsArray($receipts->data);

        if (count($receipts->data) > 0) {
            $receipt = $receipts->data[0];
            $this->assertInstanceOf(Receipt::class, $receipt);
            $this->assertNotNull($receipt->id);
            $this->assertNotNull($receipt->organization_id);
        }
    }

    public function testListReceiptsWithPagination(): void
    {
        $receipts = $this->getClient()->receipts->list(['page' => 1]);

        $this->assertInstanceOf(Collection::class, $receipts);
        $this->assertNotNull($receipts->meta);
    }

    public function testRetrieveReceipt(): void
    {
        $receipts = $this->getClient()->receipts->list(['per_page' => 1]);

        if (count($receipts->data) === 0) {
            $this->markTestSkipped('No receipts available for testing');
        }

        $receiptId = $receipts->data[0]->id;
        $receipt = $this->getClient()->receipts->retrieve($receiptId);

        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertEquals($receiptId, $receipt->id);
    }

    // -------------------------------------------------------------------------
    // Receipt Prefixes
    // -------------------------------------------------------------------------

    public function testListReceiptPrefixes(): void
    {
        $prefixes = $this->getClient()->receiptPrefixes->list();

        $this->assertInstanceOf(Collection::class, $prefixes);
        $this->assertIsArray($prefixes->data);

        if (count($prefixes->data) > 0) {
            $prefix = $prefixes->data[0];
            $this->assertInstanceOf(ReceiptPrefix::class, $prefix);
        }
    }
}
