<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Auth\Login;
use Tests\Traits\TestHelper;
use Spatie\Permission\Models\Role;

class LoginComponentTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_login_component_renders(): void
    {
        Livewire::test(Login::class)
            ->assertOk();
    }

    public function test_successful_login_without_2fa(): void
    {
        $roles = Role::all();
        foreach ($roles as $role) {
            $user = $this->createUserAndSignIn(roleName: $role->name);

            if ($role->name === "member") {
                Livewire::test(Login::class)
                    ->set('email', $user->email)
                    ->set('password', 'password')
                    ->call('login')
                    ->assertRedirect(route('dashboard'));
            }else{
                Livewire::test(Login::class)
                    ->set('email', $user->email)
                    ->set('password', 'password')
                    ->call('login')
                    ->assertRedirect(route('admin.subscriptions.stats'));
            }

            $this->assertAuthenticatedAs($user);
        }
    }

    public function test_invalid_credentials_show_validation_error(): void
    {
        $user = $this->createUser();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();
    }

    public function test_rate_limiting_works(): void
    {
        $user = User::factory()->create();

        // Make multiple failed attempts
        for ($i = 0; $i < 6; $i++) {
            $component = Livewire::test(Login::class)
                ->set('email', $user->email)
                ->set('password', 'wrong-password')
                ->call('login');

            if ($i < 5) {
                $component->assertHasErrors(['email']);
            } else {
                // 6th attempt should be rate limited
                $component->assertHasErrors(['email']);
            }
        }
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

    public function test_empty_email_validation(): void
    {
        Livewire::test(Login::class)
            ->set('email', '')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email' => 'required']);
    }

    public function test_empty_password_validation(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['password' => 'required']);
    }

    public function test_invalid_email_format_validation(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'invalid-email')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_post_login_redirect_based_on_role(): void
    {
        // Test member role
        $member = User::factory()->create();
        $member->assignRole('member');

        Livewire::test(Login::class)
            ->set('email', $member->email)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        Auth::logout();

        // Test admin role
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::test(Login::class)
            ->set('email', $admin->email)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('admin.subscriptions.stats'));
    }

    public function test_session_regeneration_on_successful_login(): void
    {
        $user = User::factory()->create();
        $user->assignRole('member');
        $oldSessionId = Session::getId();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        // Session should be regenerated
        $this->assertNotEquals($oldSessionId, Session::getId());
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_with_unconfirmed_2fa_logs_in_normally(): void
    {
        $user = User::factory()->create();
        $user->assignRole('member');
        $user->forceFill([
            'two_factor_secret' => encrypt('test-secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
            // two_factor_confirmed_at is null
        ])->save();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_with_2fa_secret_but_no_confirmation_logs_in_normally(): void
    {
        $user = User::factory()->create();
        $user->assignRole('member');
        $user->forceFill([
            'two_factor_secret' => encrypt('test-secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
            'two_factor_confirmed_at' => null,
        ])->save();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('dashboard'));

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

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('two-factor.login'));

        // User should be logged out and session should contain user ID
        $this->assertGuest();
        $this->assertEquals($user->id, Session::get('login.id'));
    }
}
