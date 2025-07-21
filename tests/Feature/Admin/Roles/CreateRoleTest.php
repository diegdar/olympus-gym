<?php
declare(strict_types=1);

namespace Tests\Feature\Admin\Roles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Database\Seeders\RoleSeeder;
use Tests\TestCase;
use Illuminate\Testing\TestResponse;
use Tests\Traits\TestHelper;

class CreateRoleTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected const PERMISSION = 'admin.roles.create';

    protected const ROUTE_CREATE_ROLE_VIEW = 'admin.roles.create';
    protected const ROUTE_STORE_ROLE = 'admin.roles.store';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);             
    }
    
    private function getCreateRoleFormAs(string $roleName): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_CREATE_ROLE_VIEW));
    }

    public function test_authorized_user_can_view_create_role_form()
    {
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION) as $authorizedRole
        ) {
            $response = $this->getCreateRoleFormAs($authorizedRole);

            $response->assertStatus(200)
                     ->assertSee('Crear role')
                     ->assertSee('Lista de permisos:');
        }
    }

    public function test_unauthorized_user_cannot_view_create_role_form()
    {
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION) as $unauthorizedRole
        ) {
            $response = $this->getCreateRoleFormAs($unauthorizedRole);

            $response->assertStatus(403)
                     ->assertDontSee('Crear role')
                     ->assertDontSee('Lista de permisos:');
        }        
    }

    private function createRoleAs(string $AuthorizedRole, array $newRoleData): TestResponse
    {
        return $this->actingAsRole($AuthorizedRole)
            ->from(route(self::ROUTE_CREATE_ROLE_VIEW))
            ->post(route(self::ROUTE_STORE_ROLE, $newRoleData));
    }    

    public function test_validation_errors_are_shown_if_name_field_is_empty()
    {
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION) as $authorizedRole
        ) {
            $response = $this->createRoleAs($authorizedRole, [
                'name' => '',
                'permissions' => [],
            ]);

            $response->assertRedirect(route(self::ROUTE_CREATE_ROLE_VIEW))->assertSessionHasErrors(['name']);
        }
    }

    public function test_validation_errors_are_shown_if_permissions_fields_are_empty()
    {
        $newRoleNumber = 1;
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION) as $authorizedRole
        ) {
            $response = $this->createRoleAs($authorizedRole, [
                'name' => 'role-name' . $newRoleNumber,
                'permissions' => [],
            ]);

            $response->assertRedirect(route(self::ROUTE_CREATE_ROLE_VIEW))->assertSessionHasErrors(['permissions']);

            ++$newRoleNumber;
        }
    }

    public function test_authorized_user_can_create_role_and_assign_permissions()
    {
        $p1 = Permission::create(['name' => 'perm-1', 'description' => 'Permiso 1']);
        $p2 = Permission::create(['name' => 'perm-2', 'description' => 'Permiso 2']);

        $newRoleNumber = 1;
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION) as $authorizedRole
        ) {
            $response = $this->createRoleAs($authorizedRole, [
                'name' => 'role-name' . $newRoleNumber,
                'permissions' => [$p1->id, $p2->id],
            ]);

            $response->assertRedirect(route('admin.roles.index'))->assertStatus(302);

            $this->assertDatabaseHas('roles', ['name' => 'role-name' . $newRoleNumber,]);
            $this->assertTrue(Role::where('name', 'role-name' . $newRoleNumber)->first()->hasPermissionTo($p1));
            $this->assertTrue(Role::where('name', 'role-name' . $newRoleNumber)->first()->hasPermissionTo($p2));

            ++$newRoleNumber;            
        }        
    }

    public function test_unauthorized_user_cannot_create_role_and_assign_permissions()
    {
        $role = Role::create(['name' => 'old-name']);
        $p1 = Permission::create(['name' => 'perm-1', 'description' => 'Permiso 1']);
        $p2 = Permission::create(['name' => 'perm-2', 'description' => 'Permiso 2']);

        $newRoleNumber = 1;
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION) as $unauthorizedRole
        ) {
            $response = $this->createRoleAs($unauthorizedRole, [
                'name' => 'role-name' . $newRoleNumber,
                'permissions' => [$p1->id, $p2->id],
            ]);

            $response->assertStatus(403);
            $this->assertDatabaseMissing('roles', [
                'id' => $role->id,
                'name' => 'role-name' . $newRoleNumber,
            ]);
            $this->assertFalse($role->fresh()->hasPermissionTo($p1));
            $this->assertFalse($role->fresh()->hasPermissionTo($p2));

            ++$newRoleNumber;
        }        
    }
}
