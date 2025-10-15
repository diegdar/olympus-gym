<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\Traits\TestHelper;

class LoginViaFormTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(302);

        $this->assertGuest();
    }

    #[DataProvider('accessProvider')]
    public function test_authenticated_users_can_access_expected_pages(string $role, string $route): void
    {
        $user = $this->createUserAndSignIn($role);

        $this->actingAs($user)
            ->get(route($route))
            ->assertStatus(200);

        $this->assertAuthenticatedAs($user);
    }

    public static function accessProvider(): array
    {
        return [
            'member can access dashboard'
              => ['member', 'dashboard'],
            'admin can access subscription stats'
              => ['admin', 'admin.subscriptions.stats'],
            'super-admin can access subscription stats'
              => ['super-admin', 'admin.subscriptions.stats'],
        ];
    }


    public function test_users_can_logout_by_role(): void
    {
        $roles = Role::pluck('name')->toArray();
        foreach ($roles as $role) {
            $user = $this->createUserAndSignIn($role);

            $response = $this->actingAs($user)->post('/logout');

            $this->assertGuest();
            $response->assertredirect(route('home'));
        }
    }

}
