<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class LoginViaGithubTest extends TestCase
{

    private const REDIRECT_ROUTE = 'admin.subscriptions.stats';

    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_redirect_to_github()
    {
        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn($this->mockSocialiteDriver());

        $response = $this->get(route('auth.github'));

        $response->assertStatus(302);
        $response->assertRedirectContains('https://github.com/login/oauth/authorize');
    }

    public function test_handle_github_callback_existing_user()
    {
        $existingUser = User::factory()->create(['email' => 'test@example.com']);

        $githubUser = (object) [
            'id' => 12345,
            'nickname' => 'testuser',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ];

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn($this->mockSocialiteDriver($githubUser));

        $response = $this->get('/auth/github/callback');

        $response->assertStatus(302);
        $response->assertRedirect(route(self::REDIRECT_ROUTE, absolute: false));
        $this->assertTrue(Auth::check());
        $this->assertAuthenticatedAs($existingUser);
    }

    public function test_handle_github_callback_not_existing_user()
    {
        $githubUser = (object) [
            'id' => 12345,
            'nickname' => 'testuser',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ];

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn($this->mockSocialiteDriver($githubUser));

        $response = $this->get(route('auth.github.callback'));

        $response->assertStatus(302);
        $response->assertRedirectContains('register?registerMessage=');
    }

    protected function mockSocialiteDriver($user = null)
    {
        $mock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $mock->shouldReceive('redirect')
            ->andReturn(redirect()->away('https://github.com/login/oauth/authorize'));
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
