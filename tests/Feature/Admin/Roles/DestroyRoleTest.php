<?php
declare(strict_types=1);

namespace Tests\Feature\Admin\Roles;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Role;

class DestroyRoleTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected const PERMISSION = 'admin.roles.destroy';
    // Routes
    protected const ROUTE_ROLE_INDEX = 'admin.roles.index';
    protected const ROUTE_DESTROY_ROLE = 'admin.roles.destroy';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function destroyRoleAs(string $roleName, int $roleIdToDestroy): TestResponse
    {
        return $this->actingAsRole($roleName)->delete(route(self::ROUTE_DESTROY_ROLE, $roleIdToDestroy));
    }

    public function test_can_destroy_a_role_as_authorized_user()
    {
        
        foreach (
            $this->getAuthorizedRoles(self::PERMISSION) 
            as $authorizedRole
        ) {
            $roleToDestroy = Role::create(['name' => 'writer']);
            $response = $this->destroyRoleAs($authorizedRole, $roleToDestroy->id);
            $response->assertStatus(302)
                     ->assertRedirect(route(self::ROUTE_ROLE_INDEX));

            $this->assertDatabaseMissing('roles', ['id' => $roleToDestroy->id]);
        }
    }

    public function test_cannot_destroy_a_role_as_unauthorized_user()
    {
        
        $roleToDestroy = Role::create(['name' => 'writer']);
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION) 
            as $unauthorizedRole
        ) {
            $response = $this->destroyRoleAs($unauthorizedRole, $roleToDestroy->id);
            $response->assertStatus(403);

            $this->assertDatabaseHas('roles', ['id' => $roleToDestroy->id]);
        }
    }

}
