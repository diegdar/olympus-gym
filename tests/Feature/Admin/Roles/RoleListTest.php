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

    // Permissions
    protected const PERMISSION_LIST_ROLES = 'admin.roles.index';
    protected const PERMISSION_CREATE_ROLE = 'admin.roles.create';
    protected const PERMISSION_EDIT_ROLE = 'admin.roles.edit';
    protected const PERMISSION_DESTROY_ROLE = 'admin.roles.destroy';

    // Routes
    protected const ROUTE_ROLE_INDEX = 'admin.roles.index';
    protected const ROUTE_CREATE_ROLE_VIEW = 'admin.roles.create';
    protected const ROUTE_EDIT_ROLE_VIEW = 'admin.roles.edit';
    protected const ROUTE_DESTROY_ROLE = 'admin.roles.destroy';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function getRolesListAs(string $roleName): TestResponse
    {
        return $this->actingAsRole($roleName)->get(route(self::ROUTE_ROLE_INDEX));
    }

    private function assertButtonVisibility(
        string $permission,
        string $text,
        string $routeName,
        mixed $routeParam = null
    ): void {
        $authorizedRoles = $this->getAuthorizedRoles($permission);
        foreach ($authorizedRoles as $role) {
            $response = $this->getRolesListAs($role);
            $response->assertStatus(200)
                     ->assertSeeText($text)
                     ->assertSee(route($routeName, $routeParam), false);
        }

        $unauthorizedRoles = $this->getUnauthorizedRoles($permission);
        foreach ($unauthorizedRoles as $role) {
            $response = $this->getRolesListAs($role);
            if ($response->status() === 200) {
                $response->assertDontSeeText($text)
                         ->assertDontSee(route($routeName, $routeParam), false);
            } else {
                $response->assertStatus(403);
            }
        }
    }

    public function test_users_with_index_permission_can_see_role_list(): void
    {
        $roles = Role::all();
        $authorizedRoles = $this->getAuthorizedRoles(self::PERMISSION_LIST_ROLES);
        foreach ($authorizedRoles as $role) {
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
        $unauthorizedRoles = $this->getUnauthorizedRoles(self::PERMISSION_LIST_ROLES);
        foreach ($unauthorizedRoles as $role) {
            $response = $this->getRolesListAs($role);

            $response->assertStatus(403)
                     ->assertDontSee('Lista de roles')
                     ->assertDontSee('ID')
                     ->assertDontSee('Role');
        }
    }

    public function test_create_button_is_visible_depends_on_permission(): void
    {
        $this->assertButtonVisibility(
            self::PERMISSION_CREATE_ROLE,
            'Crear rol',
            self::ROUTE_CREATE_ROLE_VIEW
        );
    }

    public function test_edit_button_is_visible_depends_on_permission(): void
    {
        $this->assertButtonVisibility(
            self::PERMISSION_EDIT_ROLE,
            'Editar',
            self::ROUTE_EDIT_ROLE_VIEW,
            1
        );
    }

    public function test_destroy_button_is_visible_depends_on_permission(): void
    {
        $this->assertButtonVisibility(
            self::PERMISSION_DESTROY_ROLE,
            'Borrar',
            self::ROUTE_DESTROY_ROLE,
            1
        );
    }
}
