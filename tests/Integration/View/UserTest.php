<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\User as OrganizationUser;
use Enlivy\Organization\UserRole;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Organization User-related endpoints.
 */
class UserTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Organization Users
    // -------------------------------------------------------------------------

    public function testListOrganizationUsers(): void
    {
        $users = $this->getClient()->organizationUsers->list();

        $this->assertInstanceOf(Collection::class, $users);
        $this->assertIsArray($users->data);

        if (count($users->data) > 0) {
            $user = $users->data[0];
            $this->assertInstanceOf(OrganizationUser::class, $user);
            $this->assertIdPrefix('org_user_', $user->id);
            $this->assertNotNull($user->organization_id);
        }
    }

    public function testListOrganizationUsersWithPagination(): void
    {
        $users = $this->getClient()->organizationUsers->list(['page' => 1]);

        $this->assertInstanceOf(Collection::class, $users);
        $this->assertNotNull($users->meta);
        $this->assertEquals(1, $users->getPagination()['current_page']);
    }

    public function testListOrganizationUsersWithInclude(): void
    {
        $users = $this->getClient()->organizationUsers->list([
            'include' => 'user_role',
        ]);

        $this->assertInstanceOf(Collection::class, $users);

        if (count($users->data) > 0) {
            $user = $users->data[0];
            // Role should be included if user has one
            if ($user->organization_user_role_id !== null) {
                $this->assertNotNull($user->user_role);
            }
        }
    }

    public function testRetrieveOrganizationUser(): void
    {
        $users = $this->getClient()->organizationUsers->list(['per_page' => 1]);

        if (count($users->data) === 0) {
            $this->markTestSkipped('No organization users available for testing');
        }

        $userId = $users->data[0]->id;
        $user = $this->getClient()->organizationUsers->retrieve($userId);

        $this->assertInstanceOf(OrganizationUser::class, $user);
        $this->assertEquals($userId, $user->id);
    }

    public function testRetrieveOrganizationUserWithInclude(): void
    {
        $users = $this->getClient()->organizationUsers->list(['per_page' => 1]);

        if (count($users->data) === 0) {
            $this->markTestSkipped('No organization users available for testing');
        }

        $user = $this->getClient()->organizationUsers->retrieve(
            $users->data[0]->id,
            ['include' => 'user_role']
        );

        $this->assertInstanceOf(OrganizationUser::class, $user);
    }

    // -------------------------------------------------------------------------
    // User Roles
    // -------------------------------------------------------------------------

    public function testListUserRoles(): void
    {
        $roles = $this->getClient()->userRoles->list();

        $this->assertInstanceOf(Collection::class, $roles);
        $this->assertIsArray($roles->data);
        $this->assertGreaterThan(0, count($roles->data), 'Organization should have at least one user role');

        $role = $roles->data[0];
        $this->assertInstanceOf(UserRole::class, $role);
        $this->assertNotNull($role->id);
    }

    public function testRetrieveUserRole(): void
    {
        $roles = $this->getClient()->userRoles->list(['per_page' => 1]);

        if (count($roles->data) === 0) {
            $this->markTestSkipped('No user roles available for testing');
        }

        $roleId = $roles->data[0]->id;
        $role = $this->getClient()->userRoles->retrieve($roleId);

        $this->assertInstanceOf(UserRole::class, $role);
        $this->assertEquals($roleId, $role->id);
    }
}
