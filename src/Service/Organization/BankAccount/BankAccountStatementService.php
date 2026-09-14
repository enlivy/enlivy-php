<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\BankAccount;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\BankAccountStatement;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * The statement archive kept under a bank account: one dated document per period.
 */
class BankAccountStatementService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const ?string RESOURCE_CLASS = BankAccountStatement::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'bank_account',
        'uploaded_by_user',
        'deleted_by_user',
    ];

    public const array AVAILABLE_FILTERS = [];

    /**
     * @return Collection<BankAccountStatement>
     */
    public function list(string $bankAccountId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<BankAccountStatement> */
        return $this->requestCollection('GET', $this->orgPath($orgId, "bank-accounts/{$bankAccountId}/statements"), $params, $opts);
    }

    /**
     * Upload a statement. Multipart: `file`, plus `period_start`, `period_end` and `format`.
     */
    public function create(string $bankAccountId, array $params, ?RequestOptions $opts = null): BankAccountStatement
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BankAccountStatement */
        return $this->request('POST', $this->orgPath($orgId, "bank-accounts/{$bankAccountId}/statements"), $params, $opts);
    }

    public function download(string $bankAccountId, string $statementId, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, "bank-accounts/{$bankAccountId}/statements/{$statementId}/download"), $params, $opts);
    }

    public function delete(string $bankAccountId, string $statementId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, "bank-accounts/{$bankAccountId}/statements/{$statementId}"), $params, $opts);
    }

    public function restore(string $bankAccountId, string $statementId, array $params = [], ?RequestOptions $opts = null): BankAccountStatement
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var BankAccountStatement */
        return $this->request('POST', $this->orgPath($orgId, "bank-accounts/{$bankAccountId}/statements/restore/{$statementId}"), $params, $opts);
    }
}
