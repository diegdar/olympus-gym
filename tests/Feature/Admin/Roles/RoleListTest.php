<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Roles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Role;
use Tests\Traits\TestHelper;

class RoleListTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function getRolesListAs(string $roleName): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route('admin.roles.index'));
    }

    public function test_users_with_index_permission_can_see_role_list(): void
    {
        $roles = Role::all();

        foreach (
            $this->getAuthorizedRoles('admin.roles.index')
            as $role
        ) {
            $response = $this->getRolesListAs($role);

            $response->assertStatus(200)
                     ->assertSee('Lista de roles')
                     ->assertSeeInOrder(['ID', 'Role']);

            foreach ($roles as $r) {
                $response->assertSee($r->name)
                         ->assertSee((string) $r->id);
            }
        }
    }

    public function test_users_without_index_permission_get_403(): void
    {
        foreach (
            $this->getUnauthorizedRoles('admin.roles.index')
            as $role
        ) {
            $response = $this->getRolesListAs($role);

            $response->assertStatus(403)
                     ->assertDontSee('Lista de roles')
                     ->assertDontSee('ID')
                     ->assertDontSee('Role');
        }
    }

    private function assertButtonVisibility(
        string $permission,
        string $textButton,
        string $routeName,
        mixed $routeParam = null
    ): void {
        foreach (
            $this->getAuthorizedRoles($permission) 
            as $role
        ) {
            $response = $this->getRolesListAs($role);
            $response->assertStatus(200)
                     ->assertSeeText($textButton,)
                     ->assertSee(route($routeName, $routeParam), false);
        }

        foreach (
            $this->getUnauthorizedRoles($permission) 
            as $role
        ) {
            $response = $this->getRolesListAs($role);
            if ($response->status() === 200) {
                $response->assertDontSeeText($textButton,)
                         ->assertDontSee(route($routeName, $routeParam), false);
            } else {
                $response->assertStatus(403);
            }
        }
    }    

    public function test_create_button_is_visible_depends_on_permission(): void
    {
        $this->assertButtonVisibility(
            'admin.roles.create',
            'Crear rol',
            'admin.roles.create'
        );
    }

    public function test_edit_button_is_visible_depends_on_permission(): void
    {
        $this->assertButtonVisibility(
            'admin.roles.edit',
            'Editar',
            'admin.roles.edit',
            1
        );
    }

    public function test_destroy_button_is_visible_depends_on_permission(): void
    {
        $this->assertButtonVisibility(
            'admin.roles.destroy',
            'Borrar',
            'admin.roles.destroy',
            1
        );
    }
}
