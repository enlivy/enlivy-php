<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyClient;
use Enlivy\EnlivyObject;
use Enlivy\EnlivyPortalClient;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Organization\AccessGrant;
use Enlivy\Organization\ProjectRole;
use Enlivy\Organization\Report;
use Enlivy\Organization\ResourceBundleAttachment;
use Enlivy\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AccessAndReportsTest extends TestCase
{
    private MockHttpClient $httpClient;
    private EnlivyClient $client;
    private EnlivyPortalClient $portal;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
        $this->client = new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
        ]);
        $this->portal = new EnlivyPortalClient([
            'portal_token' => 'portal_tok',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
        ]);
    }

    public function testAccessGrantLifecycle(): void
    {
        $this->httpClient->addResponse(201, ['data' => [
            'id' => 'org_acc_grant_1',
            'grantee_type' => 'project_role',
            'resource_type' => 'report_schema',
            'abilities' => ['create', 'view_own', 'edit_own'],
            'settings' => ['edit_window_seconds' => 86400],
        ]]);

        $grant = $this->client->accessGrants->create([
            'grantee_type' => 'project_role',
            'grantee_id' => 'org_proj_role_1',
            'organization_project_id' => 'org_proj_1',
            'resource_type' => 'report_schema',
            'abilities' => ['create', 'view_own', 'edit_own'],
            'settings' => ['edit_window_seconds' => 86400],
            'include' => ['grantee_project_role'],
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringEndsWith('/organizations/org_default/access-grants', $request['url']);
        $this->assertInstanceOf(AccessGrant::class, $grant);
        $this->assertSame(['create', 'view_own', 'edit_own'], $grant->abilities);

        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_acc_grant_1']]);
        $this->client->accessGrants->update('org_acc_grant_1', ['abilities' => ['view_others']]);
        $this->assertSame('PUT', $this->httpClient->getLastRequest()['method']);
        $this->assertStringEndsWith('/access-grants/org_acc_grant_1', $this->httpClient->getLastRequest()['url']);

        $this->httpClient->addResponse(200, ['data' => ['status' => 'ok']]);
        $deleted = $this->client->accessGrants->delete('org_acc_grant_1');
        $this->assertSame('DELETE', $this->httpClient->getLastRequest()['method']);
        $this->assertInstanceOf(EnlivyObject::class, $deleted);
        $this->assertNotInstanceOf(AccessGrant::class, $deleted);
    }

    public function testAccessGrantListFiltersAndIncludes(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->accessGrants->list([
            'grantee_type' => 'person',
            'grantee_id' => 'org_user_1',
            'resource_type' => 'playbook',
            'include' => ['grantee_person', 'organization_project'],
        ]);

        $params = $this->httpClient->getLastRequest()['params'];
        $this->assertSame('person', $params['grantee_type']);
        $this->assertSame('grantee_person,organization_project', $params['include']);
    }

    public function testProjectRolesLiveAtTheOrganization(): void
    {
        $this->httpClient->addResponse(201, ['data' => ['id' => 'org_proj_role_1', 'name' => 'Reviewer', 'alias' => 'reviewer']]);

        $role = $this->client->projectRoles->create(['name' => 'Reviewer', 'alias' => 'reviewer']);

        $this->assertInstanceOf(ProjectRole::class, $role);
        $this->assertStringEndsWith('/organizations/org_default/project-roles', $this->httpClient->getLastRequest()['url']);

        $this->httpClient->addResponse(200, ['data' => ['status' => 'ok']]);
        $this->assertInstanceOf(EnlivyObject::class, $this->client->projectRoles->delete('org_proj_role_1'));
        $this->assertStringEndsWith('/project-roles/org_proj_role_1', $this->httpClient->getLastRequest()['url']);
    }

    public function testResourceBundleAttachments(): void
    {
        $this->httpClient->addResponse(201, ['data' => ['id' => 'org_res_bndl_att_1', 'organization_project_id' => null]]);

        $attachment = $this->client->resourceBundleAttachments->create('org_res_bndl_1', ['organization_project_id' => null]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringEndsWith('/resource-bundles/org_res_bndl_1/attachments', $request['url']);
        $this->assertArrayHasKey('organization_project_id', $request['params']);
        $this->assertInstanceOf(ResourceBundleAttachment::class, $attachment);

        $this->httpClient->addResponse(200, ['data' => []]);
        $this->client->resourceBundleAttachments->list('org_res_bndl_1', ['organization_project_id' => '']);
        $this->assertSame('', $this->httpClient->getLastRequest()['params']['organization_project_id']);

        $this->httpClient->addResponse(200, ['data' => ['status' => 'ok']]);
        $this->client->resourceBundleAttachments->delete('org_res_bndl_1', 'org_res_bndl_att_1');
        $this->assertSame('DELETE', $this->httpClient->getLastRequest()['method']);
        $this->assertStringEndsWith('/resource-bundles/org_res_bndl_1/attachments/org_res_bndl_att_1', $this->httpClient->getLastRequest()['url']);
    }

    public function testPersonAccessReport(): void
    {
        $this->httpClient->addResponse(200, ['data' => [
            'organization_user_id' => 'org_user_1',
            'is_current' => true,
            'built_at' => '2026-10-16T10:00:00Z',
            'access' => [['resource_type' => 'guideline', 'ability' => 'view', 'organization_project_id' => null, 'resource_id' => null, 'settings' => null]],
        ]]);

        $access = $this->client->organizationUsers->access('org_user_1');

        $this->assertStringEndsWith('/users/org_user_1/access', $this->httpClient->getLastRequest()['url']);
        $this->assertTrue($access->is_current);
    }

    /**
     * @return array<string, array{string, array<string, mixed>}>
     */
    public static function honouredFilters(): array
    {
        return [
            'roles the caller may manage' => ['userRoles', ['manageable' => true]],
            'members by project role' => ['projectMembers', ['organization_project_role_id' => 'org_proj_role_1']],
            'reports awaiting acknowledgement' => ['reports', ['awaiting_acknowledgement' => true, 'status' => 'submitted', 'kind' => 'work_report']],
            'reports about an invoice' => ['reports', ['linked_type' => 'invoice', 'linked_id' => 'org_inv_1']],
            'reports for a recipient and task' => ['reports', ['recipient_organization_user_id' => 'org_user_1', 'organization_task_id' => 'org_task_1']],
            'report schemas by kind' => ['reportSchemas', ['kind' => 'acceptance']],
        ];
    }

    /**
     * @param array<string, mixed> $filters
     */
    #[DataProvider('honouredFilters')]
    public function testListSendsTheNewFilters(string $accessor, array $filters): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $accessor === 'projectMembers'
            ? $this->client->projectMembers->list('org_proj_1', $filters)
            : $this->client->{$accessor}->list($filters);

        $params = $this->httpClient->getLastRequest()['params'];
        foreach ($filters as $key => $value) {
            $this->assertSame($value, $params[$key]);
        }
    }

    public function testRetiredFiltersAndIncludesAreRefused(): void
    {
        foreach ([
            fn () => $this->client->projectMembers->list('org_proj_1', ['member_role' => 'client']),
            fn () => $this->client->projects->list(['include' => 'project_resource_bundles']),
        ] as $call) {
            try {
                $call();
                $this->fail('A retired filter or include was accepted');
            } catch (InvalidArgumentException) {
            }
        }

        $this->assertSame(0, $this->httpClient->getRequestCount());
    }

    public function testReportReadsItsLifecycle(): void
    {
        $this->httpClient->addResponse(200, ['data' => [
            'id' => 'org_rep_1',
            'status' => 'submitted',
            'acknowledgement' => 'required',
            'awaiting_acknowledgement' => true,
            'period_from' => '2026-10-01',
            'period_to' => '2026-10-15',
            'worked_minutes' => 4800,
        ]]);

        $report = $this->client->reports->retrieve('org_rep_1', [
            'include' => ['links', 'schema_snapshot', 'author_portal_access', 'report_schema.report_schema_fields'],
        ]);

        $this->assertInstanceOf(Report::class, $report);
        $this->assertTrue($report->awaiting_acknowledgement);
        $this->assertSame(4800, $report->worked_minutes);
    }

    public function testPortalAcknowledgesAReport(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_rep_1', 'status' => 'acknowledged', 'can_acknowledge' => false]]);

        $report = $this->portal->reports->acknowledge('org_rep_1');

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringEndsWith('/user-client-portal/organizations/org_default/reports/org_rep_1/acknowledge', $request['url']);
        $this->assertInstanceOf(Report::class, $report);
        $this->assertSame('acknowledged', $report->status);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function portalRefusedIncludes(): array
    {
        return [
            'report author record' => ['reports', 'organization_user_role'],
            'playbook parent' => ['playbooks', 'parent_organization_playbook'],
            'guideline project' => ['guidelines', 'organization_project'],
            'report schema organization' => ['reportSchemas', 'organization'],
            'project bundles' => ['projects', 'resource_bundles'],
        ];
    }

    #[DataProvider('portalRefusedIncludes')]
    public function testPortalRefusesAnIncludeItDoesNotOffer(string $accessor, string $include): void
    {
        $this->expectException(InvalidArgumentException::class);

        try {
            $this->portal->{$accessor}->list(['include' => $include]);
        } finally {
            $this->assertSame(0, $this->httpClient->getRequestCount());
        }
    }

    public function testPortalListsTakeTheAccessFilters(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->portal->reportSchemas->list(['access_ability' => 'create', 'access_project_id' => 'org_proj_1', 'include' => 'report_schema_fields']);

        $params = $this->httpClient->getLastRequest()['params'];
        $this->assertSame('create', $params['access_ability']);
        $this->assertSame('report_schema_fields', $params['include']);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function retiredAccessors(): array
    {
        return [
            'projectPermissionGuidelines' => ['projectPermissionGuidelines'],
            'projectPermissionPlaybooks' => ['projectPermissionPlaybooks'],
            'projectPermissionReports' => ['projectPermissionReports'],
            'projectPermissionPipelines' => ['projectPermissionPipelines'],
            'resourceBundlePermissionGuidelines' => ['resourceBundlePermissionGuidelines'],
            'resourceBundlePermissionPlaybooks' => ['resourceBundlePermissionPlaybooks'],
            'resourceBundlePermissionReports' => ['resourceBundlePermissionReports'],
        ];
    }

    #[DataProvider('retiredAccessors')]
    public function testPerModulePermissionServicesAreGone(string $accessor): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->client->{$accessor};
    }
}
