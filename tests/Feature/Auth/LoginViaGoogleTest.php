<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tests\TestCase;
use Mockery;
use Laravel\Socialite\Facades\Socialite;

class LoginViaGoogleTest extends TestCase
{
    use RefreshDatabase;

    private const REDIRECT_ROUTE = 'admin.subscriptions.stats';    

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_redirect_to_google()
    {
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($this->mockSocialiteDriver());

        $response = $this->get(route('auth.google'));

        $response->assertStatus(302);
        $response->assertRedirectContains('https://google.com/login/oauth/authorize');
    }

    public function test_handle_google_callback_existing_user()
    {
        $existingUser = User::factory()->create(['email' => 'test@example.com']);

        $googleUser = (object) [
            'id' => 12345,
            'nickname' => 'testuser',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ];

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($this->mockSocialiteDriver($googleUser));

        $response = $this->get('/auth/google/callback');

        $response->assertStatus(302);
        $response->assertRedirect(route(self::REDIRECT_ROUTE, absolute: false));
        $this->assertTrue(Auth::check());
        $this->assertAuthenticatedAs($existingUser);
    }

    public function test_handle_google_callback_not_existing_user()
    {
        $googleUser = (object) [
            'id' => 12345,
            'nickname' => 'testuser',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ];

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($this->mockSocialiteDriver($googleUser));

        $response = $this->get(route('auth.google.callback'));

        $response->assertStatus(302);
        $response->assertRedirectContains('register?registerMessage=');
    }



    protected function mockSocialiteDriver($user = null)
    {
        $mock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $mock->shouldReceive('redirect')
            ->andReturn(redirect()->away('https://google.com/login/oauth/authorize'));
        $mock->shouldReceive('user')
            ->andReturn($user ?: (object) [
                'id' => 12345,
                'nickname' => 'default_testuser',
                'name' => 'Default Test User',
                'email' => 'default_test@example.com',
                'avatar' => 'https://example.com/default_avatar.jpg',
            ]);
        return $mock;
    }    

}
