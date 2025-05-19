<?php
declare(strict_types=1);

namespace Tests\Feature\Admin\Roles;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;
use Illuminate\Testing\TestResponse;
use Tests\Helpers\RoleTestHelper;

class EditRoleTest extends TestCase
{
    use RefreshDatabase, RoleTestHelper;

    protected array $authorizedRoles;

    protected array $unauthorizedRoles;

    protected const PERMISSION_NAME = 'admin.roles.edit';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->authorizedRoles = $this->getAuthorizedRoles(self::PERMISSION_NAME);

        $this->unauthorizedRoles = $this->getUnauthorizedRoles(self::PERMISSION_NAME);              
    }   

    private function getEditRoleAs(string $roleName, Role $role): TestResponse
    {
        $user = User::factory()->create()->assignRole($roleName);
        return $this->actingAs($user)->get(route('admin.roles.edit', $role));
    }

    public function test_authorized_user_can_view_edit_role_form()
    {
        $roleToEdit = Role::create(['name' => 'test-role']);

        foreach ($this->authorizedRoles as $authorizedRole) {
            $response = $this->getEditRoleAs($authorizedRole, $roleToEdit);

            $response->assertStatus(200)
                     ->assertSee('Editar role')
                     ->assertSee('value="test-role"', false)
                     ->assertSee('Lista de permisos:');
        }
    }

    public function test_unauthorized_user_cannot_view_edit_form()
    {
        $roleToEdit = Role::create(['name' => 'test-role']);

        foreach ($this->unauthorizedRoles as $unauthorizedRole) {
            $response = $this->getEditRoleAs($unauthorizedRole, $roleToEdit);

            $response->assertStatus(403)
                     ->assertDontSee('Editar role')
                     ->assertDontSee('value="test-role"', false)
                     ->assertDontSee('Lista de permisos:');
        }        
    }

    private function submitRoleToUpdate(string $roleName, Role $role, array $data): TestResponse
    {
        return $this->actingAsRole($roleName)
            ->from(route('admin.roles.edit', $role))
            ->put(route('admin.roles.update', $role), $data);
    }    

    public function test_validation_errors_are_shown_if_name_field_is_empty()
    {
        $roleToEdit = Role::create(['name' => '']);

        foreach ($this->authorizedRoles as $authorizedRole) {
            $response = $this->submitRoleToUpdate($authorizedRole, $roleToEdit, [
                'name' => '',
                'permissions' => [],
            ]);
            $response->assertRedirect(route('admin.roles.edit', $roleToEdit))
                     ->assertSessionHasErrors(['name']);
        }
    }

    public function test_validation_errors_are_shown_if_permissions_fields_are_empty()
    {
        $role = Role::create(['name' => 'test-role']);

        foreach ($this->authorizedRoles as $authorizedRole) {
            $response = $this->submitRoleToUpdate($authorizedRole, $role, [
                'name' => 'new-name',
                'permissions' => [],
            ]);

            $response->assertRedirect(route('admin.roles.edit', $role))
                     ->assertSessionHasErrors(['permissions']);
        }
    }

    public function test_authorized_user_can_update_role_and_assign_permissions()
    {
        $role = Role::create(['name' => 'old-name']);
        $p1 = Permission::create(['name' => 'perm-1', 'description' => 'Permiso 1']);
        $p2 = Permission::create(['name' => 'perm-2', 'description' => 'Permiso 2']);

        foreach ($this->authorizedRoles as $authorizedRole) {
            $response = $this->submitRoleToUpdate($authorizedRole, $role, [
                'name' => 'new-name',
                'permissions' => [$p1->id, $p2->id],
            ]);

            $response->assertRedirect(route('admin.roles.index'))
                     ->assertStatus(302);

            $this->assertDatabaseHas('roles', [
                'id' => $role->id,
                'name' => 'new-name',
            ]);
            $this->assertTrue($role->fresh()->hasPermissionTo($p1));
            $this->assertTrue($role->fresh()->hasPermissionTo($p2));
        }        
    }

    public function test_unauthorized_user_cannot_update_role_and_assign_permissions()
    {
        $role = Role::create(['name' => 'old-name']);
        $p1 = Permission::create(['name' => 'perm-1', 'description' => 'Permiso 1']);
        $p2 = Permission::create(['name' => 'perm-2', 'description' => 'Permiso 2']);

        foreach ($this->unauthorizedRoles as $unauthorizedRole) {
            $response = $this->submitRoleToUpdate($unauthorizedRole, $role, [
                'name' => 'new-name',
                'permissions' => [$p1->id, $p2->id],
            ]);

            $response->assertStatus(403);
            $this->assertDatabaseMissing('roles', [
                'id' => $role->id,
                'name' => 'new-name',
            ]);
            $this->assertFalse($role->fresh()->hasPermissionTo($p1));
            $this->assertFalse($role->fresh()->hasPermissionTo($p2));
        }        
    }
}
