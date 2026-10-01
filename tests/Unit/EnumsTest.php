<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\Enums\BankTransaction\States as BankTransactionStates;
use Enlivy\Enums\BillingPackage\ContractPartySelections;
use Enlivy\Enums\BillingPackage\ExchangeRateGuarantees;
use Enlivy\Enums\BillingPackage\ContractPartySources;
use Enlivy\Enums\BillingPackage\OutcomeMode;
use Enlivy\Enums\BillingPackage\PortalDiscoveryMode;
use Enlivy\Enums\BillingPackage\TierPriceType;
use Enlivy\Enums\BlockedIdentifier\Sources as BlockedIdentifierSources;
use Enlivy\Enums\BlockedIdentifier\Types as BlockedIdentifierTypes;
use Enlivy\Enums\BillingSchedule\InvoiceIssueTrigger;
use Enlivy\Enums\BillingSchedule\Statuses as BillingScheduleStatuses;
use Enlivy\Enums\CheckoutSession\Modes as CheckoutSessionModes;
use Enlivy\Enums\CheckoutSession\PaymentStatuses as CheckoutSessionPaymentStatuses;
use Enlivy\Enums\CheckoutSession\Statuses as CheckoutSessionStatuses;
use Enlivy\Enums\Concern\EnumValues;
use Enlivy\Enums\Receipt\Directions as ReceiptDirections;
use Enlivy\Enums\Receipt\Sources as ReceiptSources;
use Enlivy\Enums\Import\StopReasons;
use Enlivy\Enums\Invoice\Statuses as InvoiceStatuses;
use Enlivy\Enums\Organization\EntityManifest;
use Enlivy\Enums\Organization\Environments;
use Enlivy\Enums\EventTrail\EventType as EventTrailEventType;
use Enlivy\Enums\Payment\AttentionIssue;
use Enlivy\Enums\Payment\PaymentProvider;
use Enlivy\Enums\Payment\RefundStatus;
use Enlivy\Enums\Contract\PartyIdentityRequirements;
use Enlivy\Enums\Helpdesk\InboundEmailCategories;
use Enlivy\Enums\Helpdesk\InboundEmailInterpretations;
use Enlivy\Enums\Proposal\NotificationLogTypes as ProposalNotificationLogTypes;
use Enlivy\Enums\Proposal\PaymentMethodKind;
use Enlivy\Enums\Proposal\StageActors as ProposalStageActors;
use Enlivy\Enums\Proposal\Stages as ProposalStages;
use Enlivy\Enums\Proposal\Statuses as ProposalStatuses;
use Enlivy\Enums\Tax\TaxApplicabilityReasons;
use Enlivy\Enums\Tax\TaxEventDirections;
use Enlivy\Enums\Task\TaskCancelReasons;
use Enlivy\Enums\Task\TaskParticipantRoles;
use Enlivy\Enums\Task\TaskStatuses;
use Enlivy\Enums\TenantBilling\BillingCycles;
use Enlivy\Enums\WebLinkKinds;
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
        $this->assertSame(['bank_transfer', 'card'], PaymentMethodKind::values());
        $this->assertSame(['succeeded', 'failed', 'pending'], RefundStatus::values());
        $this->assertSame(['output', 'input'], TaxEventDirections::values());
        $this->assertSame(
            ['seller_not_registered', 'outside_scope', 'domestic', 'eu_reverse_charge', 'eu_business_without_vat_id', 'eu_consumer'],
            TaxApplicabilityReasons::values(),
        );
        $this->assertSame(['live', 'sandbox'], Environments::values());
        $this->assertSame(['invoice', 'acceptance'], ExchangeRateGuarantees::values());
        $this->assertSame(
            ['contact', 'identity_document', 'civil_registry'],
            PartyIdentityRequirements::values(),
        );
        $this->assertSame(
            ['organization', 'customer', 'third_party', 'several'],
            ProposalStageActors::values(),
        );
        $this->assertSame(
            [
                'drafting',
                'awaiting_acceptance',
                'awaiting_contract',
                'awaiting_signature',
                'awaiting_payment',
                'closed',
                'rejected',
                'expired',
            ],
            ProposalStages::values(),
        );
        $this->assertSame(
            [
                'email',
                'email_seller_viewed',
                'email_seller_accepted',
                'email_seller_rejected',
                'email_seller_expired',
                'email_seller_contract_generated',
            ],
            ProposalNotificationLogTypes::values(),
        );
        $this->assertSame(
            ['email', 'email_domain', 'phone_number'],
            BlockedIdentifierTypes::values(),
        );
        $this->assertSame(
            ['organization', 'platform', 'all'],
            BlockedIdentifierSources::values(),
        );
        $this->assertSame(['inbound', 'outbound'], ReceiptDirections::values());
        $this->assertSame(['uploaded', 'generated'], ReceiptSources::values());
        $this->assertSame(['disabled', 'request', 'checkout'], PortalDiscoveryMode::values());
        $this->assertSame(
            ['usage_limit', 'ai_limit', 'consecutive_failures', 'file_unreadable'],
            StopReasons::values(),
        );
        $this->assertSame(
            [
                'pending',
                'active',
                'payment_method_required',
                'payment_failed',
                'paused',
                'completed',
                'cancelled',
            ],
            BillingScheduleStatuses::values(),
        );
        $this->assertSame(['on_generation', 'on_payment'], InvoiceIssueTrigger::values());
        $this->assertSame(['open', 'complete', 'expired'], CheckoutSessionStatuses::values());
        $this->assertSame(['unpaid', 'paid', 'no_payment_required'], CheckoutSessionPaymentStatuses::values());
        $this->assertSame(['payment', 'setup'], CheckoutSessionModes::values());
        $this->assertSame(['sale', 'funding', 'agreement'], OutcomeMode::values());
        $this->assertSame(['fixed', 'percent_of_baseline'], TierPriceType::values());
        $this->assertSame(['standard', 'custom'], ContractPartySelections::values());
        $this->assertSame(
            ['sender', 'receiver', 'assigned', 'stated'],
            ContractPartySources::values(),
        );
        $this->assertContains('agreed', ProposalStatuses::values());
        $this->assertSame(
            ['backlog', 'completed', 'unbalanced', 'trashed'],
            BankTransactionStates::values(),
        );
        $this->assertSame(
            ['not_started', 'in_progress', 'waiting', 'completed', 'cancelled'],
            TaskStatuses::values(),
        );
        $this->assertSame(['no_longer_needed', 'duplicate', 'parent_cancelled'], TaskCancelReasons::values());
        $this->assertSame(['assignee', 'follower'], TaskParticipantRoles::values());
        $this->assertSame(
            ['unclassified', 'held', 'tickets', 'marketing', 'automated', 'spam', 'failed'],
            InboundEmailCategories::values(),
        );
        $this->assertContains('quarantined', InboundEmailInterpretations::values());
        $this->assertContains('bulk', InboundEmailInterpretations::values());
        $this->assertContains('website', WebLinkKinds::values());
        $this->assertContains('directory', WebLinkKinds::values());
        $this->assertContains('needs_attention', EventTrailEventType::values());
        $this->assertSame(
            ['double_payment', 'amount_mismatch', 'checkout_not_settled', 'proforma_part_paid', 'payment_on_cancelled_proforma'],
            AttentionIssue::values(),
        );
    }

    /**
     * Task statuses became typed task stages in 3.3.0, and the entity was renamed with them.
     */
    public function testTaskStatusEntityStaysRenamed(): void
    {
        $this->assertContains('task_stage', EntityManifest::values());
        $this->assertNotContains('task_status', EntityManifest::values());
    }

    /**
     * The two cases upstream retired in 2.7.0. Pinned so a well-meaning restore has to
     * argue with a test rather than silently re-introduce a status the API never sends.
     */
    public function testRetiredBillingScheduleStatusesStayRemoved(): void
    {
        $this->assertNotContains('subscription_required', BillingScheduleStatuses::values());
        $this->assertNotContains('cancelling', BillingScheduleStatuses::values());
    }

    /**
     * The four states upstream merged away in 3.0.0. A transaction is now either settled
     * against its documents (completed) or it is not (unbalanced); nothing reports how far
     * along that reconciliation got.
     */
    public function testMergedBankTransactionStatesStayRemoved(): void
    {
        foreach (['classified', 'connected', 'connected_partially', 'danger'] as $retired) {
            $this->assertNotContains($retired, BankTransactionStates::values());
        }
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

        $this->assertGreaterThanOrEqual(175, $count, 'Expected at least 175 mirrored enums');
    }
}
