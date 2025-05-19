<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Roles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Role;
use Tests\Helpers\RoleTestHelper;

class RoleListTest extends TestCase
{
    use RefreshDatabase, RoleTestHelper;

    protected array $authorizedRoles;

    protected array $unauthorizedRoles;

    protected const PERMISSION_NAME = 'admin.roles.index';

    protected const ROUTE_LIST_ROLES = 'admin.roles.index';
    protected const ROUTE_CREATE_ROLE_VIEW = 'admin.roles.create';
    protected const ROUTE_STORE_ROLE = 'admin.roles.store';
    protected const ROUTE_EDIT_ROLE_VIEW = 'admin.roles.edit';
    protected const ROUTE_UPDATE_ROLE = 'admin.roles.update';
    protected const ROUTE_DESTROY_ROLE = 'admin.roles.destroy';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->authorizedRoles = $this->getAuthorizedRoles(self::PERMISSION_NAME);
        $this->unauthorizedRoles = $this->getUnauthorizedRoles(self::PERMISSION_NAME);           
    }

    /**
     * Retrieve the role index page as a user with the specified role.
     *
     * @param string $roleName The name of the role to assign to the user.
     * @return TestResponse The response from accessing the role index page.
     */
    private function getIndexAs(string $roleName): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_LIST_ROLES));
    }
    
    public function test_users_with_index_permission_can_see_role_list()
    {
        $roles = Role::all();
        foreach ($this->authorizedRoles as $authorizedRole) {
            $response = $this->getIndexAs($authorizedRole);

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
        foreach ($this->unauthorizedRoles as $unauthorizedRole) {
            $response = $this->getIndexAs($unauthorizedRole);

            $response->assertStatus(403)
                     ->assertDontSee('Lista de roles')
                     ->assertDontSee('ID')
                     ->assertDontSee('Role');
        }
    }
    
    public function test_create_button_visibility_depends_on_permission()
    {
        // super-admin has create permission
        $this->getIndexAs('super-admin')
             ->assertStatus(200)
             ->assertSee('Nuevo role')
             ->assertSee(route(self::ROUTE_CREATE_ROLE_VIEW));

        // admin does not
        $this->getIndexAs('admin')
             ->assertStatus(200)
             ->assertDontSee('Nuevo role')
             ->assertDontSee(route(self::ROUTE_CREATE_ROLE_VIEW));
    }
    
    public function test_action_columns_visibility_depends_on_permissions()
    {
        // super-admin
        $this->getIndexAs('super-admin')
             ->assertStatus(200)
             ->assertSee('Acción')
             ->assertSee(route(self::ROUTE_EDIT_ROLE_VIEW, 1))
             ->assertSee(route(self::ROUTE_DESTROY_ROLE, 1));

        // admin
        $this->getIndexAs('admin')
             ->assertStatus(200)
             ->assertDontSee('Acción')
             ->assertDontSee(route(self::ROUTE_EDIT_ROLE_VIEW, 1))
             ->assertDontSee(route(self::ROUTE_DESTROY_ROLE, 1));
    }
    
    public function test_flash_message_is_displayed_if_present_in_session()
    {
        $response = $this->actingAsRole('super-admin')        
            ->withSession(['msg' => 'Operación exitosa.'])
            ->get(route(self::ROUTE_LIST_ROLES));

        $response->assertStatus(200);
    }
    
    public function test_no_flash_message_when_session_empty()
    {
        $this->getIndexAs('super-admin')
             ->assertStatus(200);
    }
}