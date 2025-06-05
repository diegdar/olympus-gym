<?php
declare(strict_types=1);

namespace Tests\Feature\Admin\Roles;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\RoleTestHelper;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DestroyRoleTest extends TestCase
{
    use RefreshDatabase, RoleTestHelper;

    // authorized roles
    protected array $authRolesForRolesList;    
    protected array $authRolesForDestroyRole;    
    // unauthorized roles
    protected array $unauthRolesForRolesList;
    protected array $unauthRolesForDestroyRole;
    // Permissions
    protected const PERMISSION_LIST_ROLES = 'admin.roles.index';
    protected const PERMISSION_DESTROY_ROLE = 'admin.roles.destroy';
    // Routes
    protected const ROUTE_ROLE_INDEX = 'admin.roles.index';
    protected const ROUTE_DESTROY_ROLE = 'admin.roles.destroy';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->authRolesForRolesList = $this->getAuthorizedRoles(self::PERMISSION_LIST_ROLES);
        $this->unauthRolesForRolesList = $this->getUnauthorizedRoles(self::PERMISSION_LIST_ROLES);
        $this->authRolesForDestroyRole = $this->getAuthorizedRoles(self::PERMISSION_DESTROY_ROLE);
        $this->unauthRolesForDestroyRole = $this->getUnauthorizedRoles(self::PERMISSION_DESTROY_ROLE);
    }

    private function DestroyRoleAs(string $roleName, Role $roleToDestroy): TestResponse
    {
        return $this->actingAsRole($roleName)->delete(route(self::ROUTE_DESTROY_ROLE, $roleToDestroy));
    }

    public function test_can_destroy_role_as_authorized_user()
    {
        $roleToDestroy = Role::create(['name' => 'writer']);


        foreach ($this->authRolesForDestroyRole as $authorizedRole) {
            $response = $this->DestroyRoleAs($authorizedRole, $roleToDestroy);
            $response->assertStatus(302)
                     ->assertRedirect(route(self::ROUTE_ROLE_INDEX));

            $this->assertDatabaseMissing('roles', ['name' => $roleToDestroy]);
        }
    }

    public function test_cannot_destroy_role_as_unauthorized_user()
    {
        $roleToDestroy = Role::create(['name' => 'writer']);

        foreach ($this->unauthRolesForDestroyRole as $unauthorizedRole) {
            $response = $this->DestroyRoleAs($unauthorizedRole, $roleToDestroy);
            $response->assertStatus(403);

            $this->assertDatabaseHas('roles', ['name' => $roleToDestroy->name]);
        }
    }

}
