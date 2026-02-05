<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

class AnalyticsService extends AbstractService
{
    public function cashflow(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, 'analytics/cashflow'), $params, $opts);
    }

    public function cashflowByType(string $type, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, "analytics/cashflow/{$type}"), $params, $opts);
    }

    public function bankTransactions(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, 'bank-transactions/analytics'), $params, $opts);
    }

    public function bankTransactionsByType(string $type, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, "bank-transactions/analytics/{$type}"), $params, $opts);
    }

    public function billingSchedules(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, 'billing-schedules/analytics'), $params, $opts);
    }

    public function billingSchedulesByType(string $type, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, "billing-schedules/analytics/{$type}"), $params, $opts);
    }

    public function invoices(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, 'invoices/analytics'), $params, $opts);
    }

    public function invoicesByType(string $type, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, "invoices/analytics/{$type}"), $params, $opts);
    }

    public function payslips(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, 'payslips/analytics'), $params, $opts);
    }

    public function payslipsByType(string $type, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, "payslips/analytics/{$type}"), $params, $opts);
    }

    public function receipts(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, 'receipts/analytics'), $params, $opts);
    }

    public function receiptsByType(string $type, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, "receipts/analytics/{$type}"), $params, $opts);
    }
}
