<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Payslip;

use Enlivy\Collection;
use Enlivy\Organization\Payslip;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing payslips.
 *
 * @method Payslip restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class PayslipService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'payslips';
    protected const ?string RESOURCE_CLASS = Payslip::class;

    public const array AVAILABLE_INCLUDES = [
        'deleted_by_user',
        'organization',
        'organization_payslip_schema',
        'receiver_user',
        'sender_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_payslip_schema_id',
        'organization_receiver_user_id',
        'organization_sender_user_id',
        'paid_at_from',
        'paid_at_to',
        'issued_at_from',
        'issued_at_to',
        'created_at_from',
        'created_at_to',
        'updated_at_from',
        'updated_at_to',
    ];

    /**
     * List all payslips.
     *
     * Resource-specific filters:
     * - `status` (string: pending|approval_required|rejected|approved|paid)
     * - `paid_at_from` / `paid_at_to` (datetime) - Paid date range
     * - `issued_at_from` / `issued_at_to` (datetime) - Issued date range
     * - `created_at_from` / `created_at_to` (datetime) - Created date range
     * - `updated_at_from` / `updated_at_to` (datetime) - Updated date range
     *
     * @return Collection<Payslip>
     *
     * @see HasFilters::GLOBAL_FILTERS for global filters (q, ids, page, per_page, etc.)
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Payslip
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Payslip
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Payslip
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Payslip
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
