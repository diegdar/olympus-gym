<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Roles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Role;
use Tests\Traits\RoleTestHelper;

class RoleListTest extends TestCase
{
    use RefreshDatabase, RoleTestHelper;

    // authorized roles
    protected array $authRolesForRolesList;
    protected array $authRolesForCreateRole;    
    protected array $authRolesForEditRole;    
    protected array $authRolesForDestroyRole;    
    // unauthorized roles
    protected array $unauthRolesForRolesList;
    protected array $unauthRolesForCreateRole;
    protected array $unauthRolesForEditRole;
    protected array $unauthRolesForDestroyRole;    

    // Permissions
    protected const PERMISSION_LIST_ROLES = 'admin.roles.index';
    protected const PERMISSION_CREATE_ROLE = 'admin.roles.create';
    protected const PERMISSION_EDIT_ROLE = 'admin.roles.edit';
    protected const PERMISSION_DESTROY_ROLE = 'admin.roles.destroy';
    // Routes
    protected const ROUTE_ROLE_INDEX = 'admin.roles.index';
    protected const ROUTE_CREATE_ROLE_VIEW = 'admin.roles.create';
    protected const ROUTE_STORE_ROLE = 'admin.roles.store';
    protected const ROUTE_EDIT_ROLE_VIEW = 'admin.roles.edit';
    protected const ROUTE_UPDATE_ROLE = 'admin.roles.update';
    protected const ROUTE_DESTROY_ROLE = 'admin.roles.destroy';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->authRolesForRolesList = $this->getAuthorizedRoles(self::PERMISSION_LIST_ROLES);
        $this->unauthRolesForRolesList = $this->getUnauthorizedRoles(self::PERMISSION_LIST_ROLES);
        $this->authRolesForCreateRole = $this->getAuthorizedRoles(self::PERMISSION_CREATE_ROLE);
        $this->unauthRolesForCreateRole = $this->getUnauthorizedRoles(self::PERMISSION_CREATE_ROLE);
        $this->authRolesForEditRole = $this->getAuthorizedRoles(self::PERMISSION_EDIT_ROLE);
        $this->unauthRolesForEditRole = $this->getUnauthorizedRoles(self::PERMISSION_EDIT_ROLE);
        $this->authRolesForDestroyRole = $this->getAuthorizedRoles(self::PERMISSION_DESTROY_ROLE);
        $this->unauthRolesForDestroyRole = $this->getUnauthorizedRoles(self::PERMISSION_DESTROY_ROLE);
    }

    /**
     * Retrieve the role index page as a user with the specified role.
     *
     * @param string $roleName The name of the role to assign to the user.
     * @return TestResponse The response from accessing the role index page.
     */
    private function getRolesListAs(string $roleName): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_ROLE_INDEX));
    }
    
    public function test_users_with_index_permission_can_see_role_list()
    {
        $roles = Role::all();
        foreach ($this->authRolesForRolesList as $authorizedRole) {
            $response = $this->getRolesListAs($authorizedRole);

            $response->assertStatus(200)
                     ->assertSee('Lista de roles')
                     ->assertSeeInOrder(['ID', 'Role']);
            foreach ($roles as $role) {
                $response->assertSee($role->name)
                         ->assertSee($role->id);
            }
        }
    }
    
    public function test_users_without_index_permission_get_403()
    {
        foreach ($this->unauthRolesForRolesList as $unauthorizedRole) {
            $response = $this->getRolesListAs($unauthorizedRole);

            $response->assertStatus(403)
                     ->assertDontSee('Lista de roles')
                     ->assertDontSee('ID')
                     ->assertDontSee('Role');
        }
    }
    
    public function test_create_button_is_visible_depends_on_permission()
    {
        foreach ($this->authRolesForCreateRole as $role) {
            $this->getRolesListAs($role)
                 ->assertStatus(200)
                 ->assertSeeText('Crear role')
                 ->assertSee(route(self::ROUTE_CREATE_ROLE_VIEW), false);
        }

        foreach ($this->unauthRolesForCreateRole as $role) {
            $response = $this->getRolesListAs($role);
            if ($response->status() === 200) {
                $response->assertDontSeeText('Crear sala');
                $response->assertDontSee(route(self::ROUTE_CREATE_ROLE_VIEW), false);
            } else {
                $response->assertStatus(403);
            }
        }
    }
    
    public function test_edit_button_is_visible_depends_on_permission(): void
    {
        foreach ($this->authRolesForEditRole as $role) {
            $response = $this->getRolesListAs($role)
                 ->assertStatus(200)
                 ->assertSeeText('Acción')
                 ->assertSee(
                    route(
                        self::ROUTE_EDIT_ROLE_VIEW,
                     1),
                     false
                    );
        }

        foreach ($this->unauthRolesForEditRole as $role) {
            $response = $this->getRolesListAs($role);
            if ($response->status() === 200) {
                $response->assertDontSeeText('Acción')
                         ->assertDontSee(
                            route(
                                    self::ROUTE_EDIT_ROLE_VIEW,
                                     1),
                                       false
                            );
            } else {
                $response->assertStatus(403);
            }
        }        
    }

    public function test_destroy_button_is_visible_depends_on_permission(): void
    {
        foreach ($this->authRolesForDestroyRole as $role) {
            $response = $this->getRolesListAs($role)
                 ->assertStatus(200)
                 ->assertSeeText('Acción')
                 ->assertSee(
                    route(
                        self::ROUTE_DESTROY_ROLE,
                     1),
                     false
                    );
        }

        foreach ($this->unauthRolesForDestroyRole as $role) {
            $response = $this->getRolesListAs($role);
            if ($response->status() === 200) {
                $response->assertDontSeeText('Acción')
                         ->assertDontSee(
                            route(
                                    self::ROUTE_DESTROY_ROLE,
                                     1),
                                       false
                            );
            } else {
                $response->assertStatus(403);
            }
        }        
    }    
}