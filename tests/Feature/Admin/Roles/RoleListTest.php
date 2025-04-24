<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Roles;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleListTest extends TestCase
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
            'roles' => ['super-admin', 'admin'],
        ],
        [
            'name' => 'admin.roles.create',
            'description' => 'Crear un role',
            'roles' => ['super-admin'],
        ],
        [
            'name' => 'admin.roles.edit',
            'description' => 'Editar/ver un role',
            'roles' => ['super-admin'],
        ],
        [
            'name' => 'admin.roles.destroy',
            'description' => 'Eliminar un role',
            'roles' => ['super-admin'],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        foreach ($this->rolesToSeed as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Seed permissions and sync to roles
        foreach ($this->permissionsToSeed as $permData) {
            $permission = Permission::firstOrCreate([
                'name' => $permData['name'],
                'description' => $permData['description'],
            ]);

            $roles = Role::whereIn('name', $permData['roles'])->get();
            $permission->syncRoles($roles);
        }
    }

    /**
     * Helper to request the roles index page as a user with given role
     */
    private function getIndexAs(string $roleName)
    {
        $user = User::factory()->create()->assignRole($roleName);
        return $this->actingAs($user)->get(route('admin.roles.index'));
    }
    
    public function test_users_with_index_permission_can_see_role_list()
    {
        foreach (['super-admin', 'admin'] as $roleName) {
            $response = $this->getIndexAs($roleName);

            $response->assertStatus(200)
                     ->assertSee('Lista de roles')
                     ->assertSeeInOrder(['ID', 'Role'])
                     ->assertSee('super-admin')
                     ->assertSee('admin')
                     ->assertSee('member');
        }
    }
    
    public function test_users_without_index_permission_get_403()
    {
        $response = $this->getIndexAs('member');
        $response->assertStatus(403);
    }
    
    public function test_create_button_visibility_depends_on_permission()
    {
        // super-admin has create permission
        $this->getIndexAs('super-admin')
             ->assertStatus(200)
             ->assertSee('Nuevo role')
             ->assertSee(route('admin.roles.create'));

        // admin does not
        $this->getIndexAs('admin')
             ->assertStatus(200)
             ->assertDontSee('Nuevo role')
             ->assertDontSee(route('admin.roles.create'));
    }
    
    public function test_action_columns_visibility_depends_on_permissions()
    {
        // super-admin
        $this->getIndexAs('super-admin')
             ->assertStatus(200)
             ->assertSee('Acción')
             ->assertSee(route('admin.roles.edit', 1))
             ->assertSee(route('admin.roles.destroy', 1));

        // admin
        $this->getIndexAs('admin')
             ->assertStatus(200)
             ->assertDontSee('Acción')
             ->assertDontSee(route('admin.roles.edit', 1))
             ->assertDontSee(route('admin.roles.destroy', 1));
    }
    
    public function test_flash_message_is_displayed_if_present_in_session()
    {
        $response = $this->actingAs(
            User::factory()->create()->assignRole('super-admin')
        )
        ->withSession(['msg' => 'Operación exitosa.'])
        ->get(route('admin.roles.index'));

        $response->assertStatus(200)
                 ->assertSee('Operación exitosa.');
    }
    
    public function test_no_flash_message_when_session_empty()
    {
        $this->getIndexAs('super-admin')
             ->assertStatus(200)
             ->assertDontSee('Operación exitosa.');
    }
}
