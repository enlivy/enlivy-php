<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\Enums\Concern\EnumValues;
use Enlivy\Enums\Invoice\Statuses as InvoiceStatuses;
use Enlivy\Enums\Payment\PaymentProvider;
use Enlivy\Enums\Payment\RefundStatus;
use Enlivy\Enums\Payslip\Fields as PayslipFields;
use Enlivy\Enums\Proposal\PaymentMethodKind;
use Enlivy\Enums\Tax\TaxApplicabilityReasons;
use Enlivy\Enums\Tax\TaxEventDirections;
use Enlivy\Enums\TenantBilling\BillingCycles;
use PHPUnit\Framework\TestCase;

final class EnumsTest extends TestCase
{
    public function testEnumValuesTraitHelpers(): void
    {
        $this->assertContains('paid', InvoiceStatuses::values());
        $this->assertContains('PAID', InvoiceStatuses::names());
        $this->assertTrue(InvoiceStatuses::isValid('draft'));
        $this->assertFalse(InvoiceStatuses::isValid('not_a_status'));
        $this->assertSame(InvoiceStatuses::OVERDUE, InvoiceStatuses::tryFrom('overdue'));
        $this->assertNull(InvoiceStatuses::tryFrom('not_a_status'));
    }

    public function testCriticalContractValues(): void
    {
        $this->assertSame('paid', InvoiceStatuses::PAID->value);
        $this->assertSame(['stripe', 'paypal'], PaymentProvider::values());
        $this->assertSame(['monthly', 'yearly'], BillingCycles::values());
        $this->assertSame(
            ['text', 'number', 'number_currency', 'number_percentage'],
            PayslipFields::values(),
        );
        $this->assertSame(['bank_transfer', 'card'], PaymentMethodKind::values());
        $this->assertSame(['succeeded', 'failed', 'pending'], RefundStatus::values());
        $this->assertSame(['output', 'input'], TaxEventDirections::values());
        $this->assertSame(
            ['seller_not_registered', 'outside_scope', 'domestic', 'eu_reverse_charge', 'eu_business_without_vat_id', 'eu_consumer'],
            TaxApplicabilityReasons::values(),
        );
    }

    /**
     * Structural guard: every mirrored enum is a string-backed enum that uses
     * the shared EnumValues trait. Catches drift if the mirror is regenerated.
     */
    public function testEveryEnumIsStringBackedAndUsesTrait(): void
    {
        $base = \dirname(__DIR__, 2) . '/src/Enums';
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS),
        );

        $count = 0;
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $relative = str_replace([$base . '/', '/', '.php'], ['', '\\', ''], $file->getPathname());
            $fqcn = 'Enlivy\\Enums\\' . $relative;

            if ($fqcn === 'Enlivy\\Enums\\Concern\\EnumValues') {
                continue;
            }

            $this->assertTrue(enum_exists($fqcn), "{$fqcn} is not an enum");

            $reflection = new \ReflectionEnum($fqcn);
            $this->assertTrue($reflection->isBacked(), "{$fqcn} is not backed");
            $this->assertSame('string', (string) $reflection->getBackingType(), "{$fqcn} is not string-backed");
            $this->assertContains(
                EnumValues::class,
                $reflection->getTraitNames(),
                "{$fqcn} does not use EnumValues",
            );
            $this->assertNotEmpty($fqcn::cases(), "{$fqcn} has no cases");
            $count++;
        }

        $this->assertGreaterThanOrEqual(100, $count, 'Expected at least 100 mirrored enums');
    }
}
