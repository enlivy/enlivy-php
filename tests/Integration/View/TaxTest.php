<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\TaxClass;
use Enlivy\Organization\TaxRate;
use Enlivy\Organization\TaxType;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Tax-related endpoints.
 */
class TaxTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Tax Classes
    // -------------------------------------------------------------------------

    public function testListTaxClasses(): void
    {
        $taxClasses = $this->getClient()->taxClasses->list();

        $this->assertInstanceOf(Collection::class, $taxClasses);
        $this->assertIsArray($taxClasses->data);

        if (count($taxClasses->data) > 0) {
            $taxClass = $taxClasses->data[0];
            $this->assertInstanceOf(TaxClass::class, $taxClass);
            $this->assertNotNull($taxClass->id);
        }
    }

    public function testRetrieveTaxClass(): void
    {
        $taxClasses = $this->getClient()->taxClasses->list(['per_page' => 1]);

        if (count($taxClasses->data) === 0) {
            $this->markTestSkipped('No tax classes available for testing');
        }

        $taxClassId = $taxClasses->data[0]->id;
        $taxClass = $this->getClient()->taxClasses->retrieve($taxClassId);

        $this->assertInstanceOf(TaxClass::class, $taxClass);
        $this->assertEquals($taxClassId, $taxClass->id);
    }

    // -------------------------------------------------------------------------
    // Tax Rates
    // -------------------------------------------------------------------------

    public function testListTaxRates(): void
    {
        $taxRates = $this->getClient()->taxRates->list();

        $this->assertInstanceOf(Collection::class, $taxRates);
        $this->assertIsArray($taxRates->data);

        if (count($taxRates->data) > 0) {
            $taxRate = $taxRates->data[0];
            $this->assertInstanceOf(TaxRate::class, $taxRate);
            $this->assertNotNull($taxRate->id);
        }
    }

    public function testRetrieveTaxRate(): void
    {
        $taxRates = $this->getClient()->taxRates->list(['per_page' => 1]);

        if (count($taxRates->data) === 0) {
            $this->markTestSkipped('No tax rates available for testing');
        }

        $taxRateId = $taxRates->data[0]->id;
        $taxRate = $this->getClient()->taxRates->retrieve($taxRateId);

        $this->assertInstanceOf(TaxRate::class, $taxRate);
        $this->assertEquals($taxRateId, $taxRate->id);
    }

    // -------------------------------------------------------------------------
    // Tax Types
    // -------------------------------------------------------------------------

    public function testListTaxTypes(): void
    {
        $taxTypes = $this->getClient()->taxTypes->list();

        $this->assertInstanceOf(Collection::class, $taxTypes);
        $this->assertIsArray($taxTypes->data);

        if (count($taxTypes->data) > 0) {
            $taxType = $taxTypes->data[0];
            $this->assertInstanceOf(TaxType::class, $taxType);
            $this->assertNotNull($taxType->id);
        }
    }

    public function testRetrieveTaxType(): void
    {
        $taxTypes = $this->getClient()->taxTypes->list(['per_page' => 1]);

        if (count($taxTypes->data) === 0) {
            $this->markTestSkipped('No tax types available for testing');
        }

        $taxTypeId = $taxTypes->data[0]->id;
        $taxType = $this->getClient()->taxTypes->retrieve($taxTypeId);

        $this->assertInstanceOf(TaxType::class, $taxType);
        $this->assertEquals($taxTypeId, $taxType->id);
    }
}
