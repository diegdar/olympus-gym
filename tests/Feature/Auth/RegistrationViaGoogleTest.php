<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class RegistrationViaGoogleTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_to_google()
    {
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($this->mockSocialiteDriver());

        $response = $this->get(route('auth.google'));

        $response->assertStatus(543);
        $response->assertRedirectContains('https://google.com/login/oauth/authorize');
    }

    public function test_handle_google_callback_new_user()
    {
        $googleUser = (object) [
            'id' => 12345,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ];

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($this->mockSocialiteDriver($googleUser));

        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);

        $response = $this->get(route('auth.google.callback'));

        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertTrue(Auth::check());
        $this->assertAuthenticatedAs(User::where('email', 'test@example.com')->first());
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_handle_google_callback_existing_user()
    {
        $existingUser = User::factory()->create(['email' => 'test@example.com']);

        $googleUser = (object) [
            'id' => 12345,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ];

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($this->mockSocialiteDriver($googleUser));

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);

        $response = $this->get('/auth/google/callback');

        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertTrue(Auth::check());
        $this->assertAuthenticatedAs($existingUser);
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    protected function mockSocialiteDriver($user = null)
    {
        $mock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $mock->shouldReceive('redirect')
            ->andReturn(redirect()->away('https://google.com/login/oauth/authorize'));
        $mock->shouldReceive('user')
            ->andReturn($user ?: (object) [
                'id' => 12345,
                'name' => 'Default Test User',
                'email' => 'default_test@example.com',
                'avatar' => 'https://example.com/default_avatar.jpg',
            ]);
        return $mock;
    }
}
