<?php
declare(strict_types=1);

namespace Tests\Feature\Admin\Roles;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EditRoleTest extends TestCase
{
    use RefreshDatabase;

    protected array $rolesToSeed = [
        'super-admin',
        'admin',
        'member',
    ];

    protected array $permissionsToSeed = [
        [
            'name' => 'admin.roles.index',
            'description' => 'Ver listado de roles',
        ],
        [
            'name' => 'admin.roles.edit',
            'description' => 'Editar/ver un role',
        ],
        [
            'name' => 'admin.roles.update',
            'description' => 'Actualizar un role',
        ],
    ];

    protected array $authorizedRoles = [
        'super-admin',
        'admin',
    ];

    protected array $unauthorizedRoles = [
        'member',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // create roles
        foreach ($this->rolesToSeed as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // create permissions and sync to roles
        foreach ($this->permissionsToSeed as $permData) {
            $permission = Permission::firstOrCreate([
                'name' => $permData['name'],
                'description' => $permData['description'] ?? null,
            ]);

            $roles = Role::whereIn('name', $this->authorizedRoles)->get();
            $permission->syncRoles($roles);
        }
    }

    private function actingAsRole(string $roleName): self
    {
        $user = User::factory()->create()->assignRole($roleName);
        return $this->actingAs($user);
    }    

    public function test_authorized_user_can_view_edit_role_form()
    {
        $roleToEdit = Role::create(['name' => 'test-role']);

        foreach ($this->authorizedRoles as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole);
            $response = $user->get(route('admin.roles.edit', $roleToEdit));

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
            $user = $this->actingAsRole($unauthorizedRole);
            $response = $user->get(route('admin.roles.edit', $roleToEdit));

            $response->assertStatus(403)
                     ->assertDontSee('Editar role')
                     ->assertDontSee('value="test-role"', false)
                     ->assertDontSee('Lista de permisos:');
        }        
    }

    public function test_validation_errors_are_shown_if_name_field_is_empty()
    {
        $role = Role::create(['name' => 'test-role']);

        foreach ($this->authorizedRoles as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole);
            $response = $user->from(route('admin.roles.edit', $role))
                             ->put(route('admin.roles.update', $role), [
                                 'name' => '',
                                 'permissions' => [],
                             ]);

            $response->assertRedirect(route('admin.roles.edit', $role))
                     ->assertSessionHasErrors(['name']);
        }
    }

    public function test_validation_errors_are_shown_if_permissions_fields_are_empty()
    {
        $role = Role::create(['name' => 'test-role']);

        foreach ($this->authorizedRoles as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole);
            $response = $user->from(route('admin.roles.edit', $role))
                             ->put(route('admin.roles.update', $role), [
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
            $user = $this->actingAsRole($authorizedRole);
            $response = $user->from(route('admin.roles.edit', $role))
                             ->put(route('admin.roles.update', $role), [
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
            $user = $this->actingAsRole($unauthorizedRole);
            $response = $user->from(route('admin.roles.edit', $role))
                             ->put(route('admin.roles.update', $role), [
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
