<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Invoice;
use Enlivy\Organization\InvoicePrefix;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Invoice-related endpoints.
 */
class InvoiceTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Invoices
    // -------------------------------------------------------------------------

    public function testListInvoices(): void
    {
        $invoices = $this->getClient()->invoices->list();

        $this->assertInstanceOf(Collection::class, $invoices);
        $this->assertIsArray($invoices->data);

        if (count($invoices->data) > 0) {
            $invoice = $invoices->data[0];
            $this->assertInstanceOf(Invoice::class, $invoice);
            $this->assertIdPrefix('org_inv_', $invoice->id);
            $this->assertNotNull($invoice->organization_id);
            $this->assertNotNull($invoice->currency);
            $this->assertNotNull($invoice->status);
        }
    }

    public function testListInvoicesWithPagination(): void
    {
        $invoices = $this->getClient()->invoices->list(['page' => 1]);

        $this->assertInstanceOf(Collection::class, $invoices);
        $this->assertNotNull($invoices->meta);

        $pagination = $invoices->getPagination();
        $this->assertNotNull($pagination);
        $this->assertEquals(1, $pagination['current_page']);
    }

    public function testListInvoicesWithStatusFilter(): void
    {
        $invoices = $this->getClient()->invoices->list(['status' => 'draft']);

        $this->assertInstanceOf(Collection::class, $invoices);

        foreach ($invoices->data as $invoice) {
            $this->assertEquals('draft', $invoice->status);
        }
    }

    public function testListInvoicesWithInclude(): void
    {
        $invoices = $this->getClient()->invoices->list([
            'include' => 'receiver_user,sender_user,line_items',
        ]);

        $this->assertInstanceOf(Collection::class, $invoices);

        if (count($invoices->data) > 0) {
            $invoice = $invoices->data[0];
            // Included relations should be present
            if ($invoice->organization_receiver_user_id !== null) {
                $this->assertNotNull($invoice->receiver_user);
            }
        }
    }

    public function testRetrieveInvoice(): void
    {
        // First get an invoice ID
        $invoices = $this->getClient()->invoices->list(['per_page' => 1]);

        if (count($invoices->data) === 0) {
            $this->markTestSkipped('No invoices available for testing');
        }

        $invoiceId = $invoices->data[0]->id;

        // Now retrieve it
        $invoice = $this->getClient()->invoices->retrieve($invoiceId);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals($invoiceId, $invoice->id);
        $this->assertNotNull($invoice->currency);
        $this->assertNotNull($invoice->status);
    }

    public function testRetrieveInvoiceWithInclude(): void
    {
        $invoices = $this->getClient()->invoices->list(['per_page' => 1]);

        if (count($invoices->data) === 0) {
            $this->markTestSkipped('No invoices available for testing');
        }

        $invoice = $this->getClient()->invoices->retrieve(
            $invoices->data[0]->id,
            ['include' => 'line_items,receiver_user']
        );

        $this->assertInstanceOf(Invoice::class, $invoice);
        // line_items should be included
        $this->assertNotNull($invoice->line_items);
    }

    // -------------------------------------------------------------------------
    // Invoice Prefixes
    // -------------------------------------------------------------------------

    public function testListInvoicePrefixes(): void
    {
        $prefixes = $this->getClient()->invoicePrefixes->list();

        $this->assertInstanceOf(Collection::class, $prefixes);
        $this->assertIsArray($prefixes->data);

        if (count($prefixes->data) > 0) {
            $prefix = $prefixes->data[0];
            $this->assertInstanceOf(InvoicePrefix::class, $prefix);
            $this->assertNotNull($prefix->id);
        }
    }
}
