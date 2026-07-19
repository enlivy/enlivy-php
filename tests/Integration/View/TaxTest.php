<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\TaxClass;
use Enlivy\Organization\TaxEvent;
use Enlivy\Organization\TaxFilingPeriod;
use Enlivy\Organization\TaxRate;
use Enlivy\Organization\TaxRegistration;
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

    // -------------------------------------------------------------------------
    // Tax Registrations
    // -------------------------------------------------------------------------

    public function testListTaxRegistrations(): void
    {
        $registrations = $this->getClient()->taxRegistrations->list();

        $this->assertInstanceOf(Collection::class, $registrations);
        $this->assertIsArray($registrations->data);

        if (count($registrations->data) > 0) {
            $this->assertInstanceOf(TaxRegistration::class, $registrations->data[0]);
            $this->assertNotNull($registrations->data[0]->id);
        }
    }

    public function testRetrieveTaxRegistration(): void
    {
        $registrations = $this->getClient()->taxRegistrations->list(['per_page' => 1]);

        if (count($registrations->data) === 0) {
            $this->markTestSkipped('No tax registrations available for testing');
        }

        $id = $registrations->data[0]->id;
        $registration = $this->getClient()->taxRegistrations->retrieve($id);

        $this->assertInstanceOf(TaxRegistration::class, $registration);
        $this->assertEquals($id, $registration->id);
    }

    public function testSuggestedTaxRegistrations(): void
    {
        $suggested = $this->getClient()->taxRegistrations->suggested();

        $this->assertNotNull($suggested);
    }

    // -------------------------------------------------------------------------
    // Tax Events
    // -------------------------------------------------------------------------

    public function testListTaxEvents(): void
    {
        $events = $this->getClient()->taxEvents->list();

        $this->assertInstanceOf(Collection::class, $events);
        $this->assertIsArray($events->data);

        if (count($events->data) > 0) {
            $this->assertInstanceOf(TaxEvent::class, $events->data[0]);
            $this->assertNotNull($events->data[0]->id);
        }
    }

    public function testRetrieveTaxEvent(): void
    {
        $events = $this->getClient()->taxEvents->list(['per_page' => 1]);

        if (count($events->data) === 0) {
            $this->markTestSkipped('No tax events available for testing');
        }

        $id = $events->data[0]->id;
        $event = $this->getClient()->taxEvents->retrieve($id);

        $this->assertInstanceOf(TaxEvent::class, $event);
        $this->assertEquals($id, $event->id);
    }

    // -------------------------------------------------------------------------
    // Tax Filing Periods
    // -------------------------------------------------------------------------

    public function testListTaxFilingPeriods(): void
    {
        $periods = $this->getClient()->taxFilingPeriods->list();

        $this->assertInstanceOf(Collection::class, $periods);
        $this->assertIsArray($periods->data);

        if (count($periods->data) > 0) {
            $this->assertInstanceOf(TaxFilingPeriod::class, $periods->data[0]);
            $this->assertNotNull($periods->data[0]->id);
        }
    }

    public function testRetrieveTaxFilingPeriod(): void
    {
        $periods = $this->getClient()->taxFilingPeriods->list(['per_page' => 1]);

        if (count($periods->data) === 0) {
            $this->markTestSkipped('No tax filing periods available for testing');
        }

        $id = $periods->data[0]->id;
        $period = $this->getClient()->taxFilingPeriods->retrieve($id);

        $this->assertInstanceOf(TaxFilingPeriod::class, $period);
        $this->assertEquals($id, $period->id);
    }

    public function testListTaxFilingPeriodPayments(): void
    {
        $periods = $this->getClient()->taxFilingPeriods->list(['per_page' => 1]);

        if (count($periods->data) === 0) {
            $this->markTestSkipped('No tax filing periods available for testing');
        }

        $payments = $this->getClient()->taxFilingPeriodPayments->list($periods->data[0]->id);

        $this->assertInstanceOf(Collection::class, $payments);
        $this->assertIsArray($payments->data);
    }
}
