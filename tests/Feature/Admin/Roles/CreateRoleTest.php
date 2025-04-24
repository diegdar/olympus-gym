<?php
declare(strict_types=1);

namespace Tests\Feature\Roles;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CreateRoleTest extends TestCase
{
    use RefreshDatabase;

    protected array $rolesToSeed = [
        'super-admin',
        'admin',
        'member',
        'guest',
    ];

    protected array $permissionsToSeed = [
        [
            'name' => 'admin.roles.index',
            'description' => 'Ver listado de roles',
        ],
        [
            'name' => 'admin.roles.create',
            'description' => 'Editar/ver un role',
        ],
        [
            'name' => 'admin.roles.store',
            'description' => 'Actualizar un role',
        ],
    ];

    protected array $authorizedRoles = [
        'super-admin',
        'admin',
    ];

    protected array $unauthorizedRoles = [
        'member',
        'guest',
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

    public function test_authorized_user_can_view_create_role_form()
    {
        foreach ($this->authorizedRoles as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole);
            $response = $user->get(route('admin.roles.create'));

            $response->assertStatus(200)
                     ->assertSee('Crear role')
                     ->assertSee('Lista de permisos:');
        }
    }

    public function test_unauthorized_user_cannot_view_create_role_form()
    {
        foreach ($this->unauthorizedRoles as $unauthorizedRole) {
            $user = $this->actingAsRole($unauthorizedRole);
            $response = $user->get(route('admin.roles.create'));

            $response->assertStatus(403)
                     ->assertDontSee('Crear role')
                     ->assertDontSee('Lista de permisos:');
        }        
    }

    public function test_validation_errors_are_shown_if_name_field_is_empty()
    {
        foreach ($this->authorizedRoles as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole);
            $response = $user->from(route('admin.roles.create'))
                ->post(route('admin.roles.store'), [
                        'name' => '',
                        'permissions' => [],
                    ]);

            $response->assertRedirect(route('admin.roles.create'))->assertSessionHasErrors(['name']);
        }
    }

    public function test_validation_errors_are_shown_if_permissions_fields_are_empty()
    {
        $newRoleNumber = 1;
        foreach ($this->authorizedRoles as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole);
            $response = $user->from(route('admin.roles.create'))
                ->post(route('admin.roles.store'), [
                        'name' => 'role-name' . $newRoleNumber,
                        'permissions' => [],
                    ]);

            $response->assertRedirect(route('admin.roles.create'))->assertSessionHasErrors(['permissions']);

            ++$newRoleNumber;
        }
    }

    public function test_authorized_user_can_create_role_and_assign_permissions()
    {
        $p1 = Permission::create(['name' => 'perm-1', 'description' => 'Permiso 1']);
        $p2 = Permission::create(['name' => 'perm-2', 'description' => 'Permiso 2']);

        $newRoleNumber = 1;
        foreach ($this->authorizedRoles as $authorizedRole) {
            $user = $this->actingAsRole($authorizedRole);
            $response = $user->from(route('admin.roles.create'))
                ->post(route('admin.roles.store'), [
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
        foreach ($this->unauthorizedRoles as $unauthorizedRole) {
            $user = $this->actingAsRole($unauthorizedRole);
            $response = $user->from(route('admin.roles.create'))
                ->post(route('admin.roles.store', $role), [
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
