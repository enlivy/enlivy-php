<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Service\AbstractServiceFactory;

class PortalServiceFactory extends AbstractServiceFactory
{
    protected function getServiceMap(): array
    {
        return [
            'profile' => ProfileService::class,
            'invoices' => InvoiceService::class,
            'receipts' => ReceiptService::class,
            'payslips' => PayslipService::class,
            'contracts' => ContractService::class,
            'networkExchanges' => NetworkExchangeService::class,
            'reports' => ReportService::class,
            'reportSchemas' => ReportSchemaService::class,
            'guidelines' => GuidelineService::class,
            'playbooks' => PlaybookService::class,
            'billingPackages' => BillingPackageService::class,
            'billingSchedules' => BillingScheduleService::class,
            'proposals' => ProposalService::class,
            'paymentMethods' => PaymentMethodService::class,
            'projects' => ProjectService::class,
            'prospects' => ProspectService::class,
            'session' => SessionService::class,
        ];
    }
}
