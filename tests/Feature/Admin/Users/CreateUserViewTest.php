<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Users;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class CreateUserViewTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected array $authRolesToCreateUser;
    protected array $unauthRolesToCreateUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->authRolesToCreateUser = $this->getAuthorizedRoles('admin.users.create');
        $indexAuthorized = $this->getAuthorizedRoles('admin.users.index');
        $createAuthorized = $this->getAuthorizedRoles('admin.users.create');
        $this->unauthRolesToCreateUser = array_diff($indexAuthorized, $createAuthorized);
    }

    public function test_create_user_form_is_visible_for_authorized_users()
    {
        foreach ($this->authRolesToCreateUser as $authorizedRole) {
            $user = $this->createUserAndSignIn($authorizedRole);

            $response = $this->actingAs($user)->get(route('admin.users.index'));

            $response->assertStatus(200)
                ->assertSee('Crear usuario')
                ->assertSee('Nombre')
                ->assertSee('Email')
                ->assertSee('Role')
                ->assertSee('-Selecciona un rol-')
                ->assertSee('Fecha nacimiento');
        }
    }

    public function test_create_user_form_is_hidden_for_unauthorized_users()
    {
        foreach ($this->unauthRolesToCreateUser as $unauthorizedRole) {
            $user = $this->createUserAndSignIn($unauthorizedRole);

            $response = $this->actingAs($user)->get(route('admin.users.index'));

            $response->assertStatus(200)
                ->assertDontSee('Crear usuario');
        }
    }

}
