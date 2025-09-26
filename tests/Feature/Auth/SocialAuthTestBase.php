<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use \Laravel\Socialite\Contracts\Provider;

abstract class SocialAuthTestBase extends TestCase
{
    use RefreshDatabase;

    protected string $providerName;
    protected string $redirectUrl;
    protected string $authRouteName;
    protected string $callbackRouteName;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Returns a mock of the Socialite provider with the given user object.
     * The mock will return a redirect to the provider authorization URL when the redirect method is called.
     * The mock will return the given user object when the user method is called.
     *
     * @param object|null $user The user object to return when the user method is called.
     * @return \Laravel\Socialite\Contracts\Provider
     */
    protected function mockSocialiteDriver(?object $user = null)
    {
        $mock = Mockery::mock(Provider::class);
        $mock->shouldReceive('redirect')
            ->andReturn(redirect()->away($this->redirectUrl));
        $mock->shouldReceive('user')
            ->andReturn($user);
        return $mock;
    }

    /**
     * Sets up the dynamic mock for the Socialite provider driver.
     * This mock returns a unique user object for each call.
     *
     * @param array $roles All available role names.
     */
    private function mockSocialiteWithDynamicUser(array $roles)
    {
        $userNumber = 0;

        Socialite::shouldReceive('driver')
            ->with($this->providerName)
            ->times(count($roles)) // Expect one call per role
            ->andReturnUsing(function () use (&$userNumber) {

                // Provider user data must be unique per iteration
                $providerUser = (object) [
                    'id' => 12345 + $userNumber,
                    'nickname' => "test{$userNumber}",
                    'name' => "Example test{$userNumber}",
                    'email' => "test{$userNumber}@example.com",
                    'avatar' => 'https://example.com/avatar.jpg',
                ];

                $userNumber++;

                return $this->mockSocialiteDriver($providerUser);
            });
    }

    public function test_redirect_to_provider()
    {
        Socialite::shouldReceive('driver')
            ->with($this->providerName)
            ->andReturn($this->mockSocialiteDriver());

        $response = $this->get(route($this->authRouteName));

        $response->assertStatus(302);
        $response->assertRedirectContains($this->redirectUrl);
    }

    public function test_handle_callback_existing_user()
    {
        $roles = Role::all()->pluck('name')->toArray();

        $this->mockSocialiteWithDynamicUser($roles);

        $userNumber = 0;
        foreach ($roles as $roleName) {

            $existingUser = User::factory()->create(['email' => "test{$userNumber}@example.com"])->assignRole($roleName);

            $response = $this->get(route($this->callbackRouteName));

            $response->assertStatus(302);
            if ($existingUser->hasRole('member')) {
                $response->assertRedirect(route('dashboard', absolute: false));
            } else {
                $response->assertRedirect(route('admin.subscriptions.stats', absolute: false));
            }

            $this->assertTrue(Auth::check());
            $this->assertAuthenticatedAs($existingUser);

            Auth::logout();
            $userNumber++;
        }
    }

    public function test_handle_callback_not_existing_user()
    {
        $providerUser = (object) [
            'id' => 12345,
            'nickname' => 'testuser',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ];

        Socialite::shouldReceive('driver')
            ->with($this->providerName)
            ->andReturn($this->mockSocialiteDriver($providerUser));

        $response = $this->get(route($this->callbackRouteName));

        $response->assertStatus(302);
        $response->assertRedirectContains('register?registerMessage=');
    }
}