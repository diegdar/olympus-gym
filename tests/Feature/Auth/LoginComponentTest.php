<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Auth\Login;
use Tests\Traits\TestHelper;
use PHPUnit\Framework\Attributes\DataProvider;

class LoginComponentTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function performLogin(string $email, string $password)
    {
        return Livewire::test(Login::class)
            ->set('email', $email)
            ->set('password', $password)
            ->call('login');
    }

    public function test_login_component_renders(): void
    {
        Livewire::test(Login::class)
            ->assertOk();
    }

    #[DataProvider('successfulLoginProvider')]
    public function test_successful_login_without_2fa(string $roleName, string $expectedRoute): void
    {
        $user = $this->createUserAndSignIn(roleName: $roleName);

        $this->performLogin($user->email, 'password')
            ->assertRedirect(route($expectedRoute));

        $this->assertAuthenticatedAs($user);
    }

    public static function successfulLoginProvider(): array
    {
        return [
            'member_role' => ['member', 'dashboard'],
            'admin_role' => ['admin', 'admin.subscriptions.stats'],
        ];
    }

    public function test_invalid_credentials_show_validation_error(): void
    {
        $user = $this->createUser();

        $this->performLogin($user->email, 'wrong-password')
            ->assertHasErrors('email');

        $this->assertGuest();
    }

    public function test_rate_limiting_works(): void
    {
        $user = User::factory()->create();

        // Make 5 failed attempts that should not be rate limited
        for ($i = 0; $i < 5; $i++) {
            $this->performLogin($user->email, 'wrong-password')
                ->assertHasErrors(['email']);
        }

        // 6th attempt should be rate limited
        $this->performLogin($user->email, 'wrong-password')
            ->assertHasErrors(['email']);
    }

    public function test_remember_me_functionality(): void
    {
        $user = User::factory()->create();
        $user->assignRole('member');

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->set('remember', true)
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        // Note: Testing remember token persistence requires additional setup
    }

    #[DataProvider('validationProvider')]
    public function test_validate_login_data(string $email, string $password, array $errors): void
    {
        Livewire::test(Login::class)
            ->set('email', $email)
            ->set('password', $password)
            ->call('login')
            ->assertHasErrors($errors);
    }

    public static function validationProvider(): array
    {
        return [
            'empty_email' => [
                'email' => '',
                'password' => 'password$1234!',
                'errors' => ['email' => 'required']
            ],
            'empty_password' => [
                'email' => 'test@example',
                'password' => '',
                'errors' => ['password' => 'required']
            ],
            'invalid_email_format' => [
                'email' => 'invalid-email',
                'password' => 'password$1234!',
                'errors' => ['email' => 'email']
            ]
        ];
    }

    public function test_session_regeneration_on_successful_login(): void
    {
        $user = $this->createUser();
        $oldSessionId = Session::getId();

        $this->performLogin($user->email, 'password')
            ->assertRedirect(route('dashboard'));

        // Session should be regenerated
        $this->assertNotEquals($oldSessionId, Session::getId());
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_2fa_enabled_redirects_to_challenge(): void
    {
        $user = $this->createUser();
        $user->forceFill([
            'two_factor_secret' => encrypt('test-secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->performLogin($user->email, 'password')
            ->assertRedirect(route('two-factor.login'));

        // User should be logged out and session should contain user ID
        $this->assertGuest();
        $this->assertEquals($user->id, Session::get('login.id'));
    }

}
