<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;

class LoginViaFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
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
        ])->assertStatus(405);

        $this->assertGuest();
    }

    #[DataProvider('accessProvider')]
    public function test_authenticated_users_can_access_expected_pages(string $role, string $path): void
    {
        $user = $this->makeUserWithRole($role);

        $this->actingAs($user)
            ->get($path)
            ->assertStatus(200);

        $this->assertAuthenticatedAs($user);
    }

    public function test_users_can_logout_by_role(): void
    {
        $roles = Role::pluck('name')->toArray();
        foreach ($roles as $role) {
            $user = $this->makeUserWithRole($role);
    
            $response = $this->actingAs($user)->post('/logout');
    
            $this->assertGuest();
            $response->assertredirect(route('home'));            
        }
    }

    /**
     * Create a fresh user and assign the given role.
     */
    private function makeUserWithRole(string $role): User
    {
        return User::factory()->create()->assignRole($role);
    }

    /**
     * Roles and the path each role must be able to access successfully.
     * super-admin and admin must access subscription stats;
     * member must access dashboard.
     */
    public static function accessProvider(): array
    {
        return [
            'member can access dashboard'        => ['member', route('dashboard', absolute: false)],
            'admin can access subscription stats' => ['admin', route('admin.subscriptions.stats', absolute: false)],
            'super-admin can access subscription stats' => ['super-admin', route('admin.subscriptions.stats', absolute: false)],
        ];
    }
}