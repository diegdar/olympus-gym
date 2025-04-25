<?php
declare(strict_types=1);

namespace Tests\Feature\Admin\Activities;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityListTest extends TestCase
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

        $this->createRoles();
        $this->createPermissionsAndSyncToRoles();
    }

    private function createRoles()
    {
        foreach ($this->rolesToSeed as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }

    private function createPermissionsAndSyncToRoles()
    {
        foreach ($this->permissionsToSeed as $permData) {
            $permission = Permission::firstOrCreate([
                'name' => $permData['name'],
                'description' => $permData['description'] ?? null,
            ]);

            $roles = Role::whereIn('name', $permData['roles'])->get();
            $permission->syncRoles($roles);
        }
    }

    private function actingAsRole(string $roleName): self
    {
        $user = User::factory()->create()->assignRole($roleName);
        return $this->actingAs($user);
    }

    private function getActivitiesIndexAs(string $roleName)
    {
        $user = User::factory()->create()->assignRole($roleName);
        return $this->actingAs($user)->get(route('activities.index'));
    }

    public function test_authorized_user_can_see_activity_list()
    {
        $this->createRoles();
        $this->createPermissionsAndSyncToRoles();

        foreach ($this->authorizedRoles as $roleName) {
            $response = $this->getActivitiesIndexAs($roleName);

            $response->assertStatus(200)
                     ->assertSee('Lista de actividades')
                     ->assertSeeInOrder(['ID', 'Actividad'])
                     ->assertSee('super-admin')
                     ->assertSee('admin')
                     ->assertSee('member');
        }
    }

    public function test_unauthorized_user_gets_403()
    {
        foreach ($this->unauthorizedRoles as $roleName) {
            $response = $this->getActivitiesIndexAs($roleName);
            $response->assertStatus(403);
        }
    }

    public function test_create_button_visibility_depends_on_permission()
    {
        foreach ($this->authorizedRoles as $roleName) {
            $response = $this->getActivitiesIndexAs($roleName);
            $response->assertStatus(200)
                     ->assertSee('Nueva actividad')
                     ->assertSee(route('admin.activities.create'));
        }

        foreach ($this->unauthorizedRoles as $roleName) {
            $response = $this->getActivitiesIndexAs($roleName);
            $response->assertStatus(200)
                     ->assertDontSee('Nueva actividad')
                     ->assertDontSee(route('admin.activities.create'));
        }
    }

    public function test_action_columns_visibility_depends_on_permissions()
    {
        foreach ($this->authorizedRoles as $roleName) {
            $response = $this->getActivitiesIndexAs($roleName);
            $response->assertStatus(200)
                     ->assertSee('Editar')
                     ->assertSee('Eliminar');
        }

        foreach ($this->unauthorizedRoles as $roleName) {
            $response = $this->getActivitiesIndexAs($roleName);
            $response->assertStatus(200)
                     ->assertDontSee('Editar')
                     ->assertDontSee('Eliminar');
        }
    }

    public function test_flash_message_is_displayed_if_present_in_session()
    {
        foreach ($this->authorizedRoles as $roleName) {
            $response = $this->actingAsRole($roleName)
                             ->withSession(['msg' => 'Test message'])
                             ->get(route('admin.activities.index'));

            $response->assertStatus(200)
                     ->assertSee('Test message');
        }
    }

    public function test_flash_message_is_not_displayed_if_not_present_in_session()
    {
        foreach ($this->authorizedRoles as $roleName) {
            $response = $this->actingAsRole($roleName)
                             ->get(route('admin.activities.index'));

            $response->assertStatus(200)
                     ->assertDontSee('Test message');
        }
    }

}
