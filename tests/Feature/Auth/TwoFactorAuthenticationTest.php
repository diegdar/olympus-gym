<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Tests\Traits\TestHelper;
use Database\Seeders\RoleSeeder;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Livewire\Livewire;
use App\Livewire\Settings\TwoFactorAuthentication;
use Illuminate\Support\Facades\Session;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    const TEST_SECRET = 'JBSWY3DPEHPK3PXP';
    const TEST_SECRET_ALT = 'test-secret';
    const VALID_TOTP_CODE = '123456';
    const INVALID_CODE = 'invalid';
    const INVALID_SHORT_CODE = '12345';
    const INVALID_RECOVERY_CODE = 'invalid-code';
    const RECOVERY_CODE_1 = 'code1';
    const RECOVERY_CODE_2 = 'code2';
    const RECOVERY_CODE_3 = 'code3';
    const RECOVERY_CODES = [self::RECOVERY_CODE_1, self::RECOVERY_CODE_2];
    const RECOVERY_CODES_THREE = [self::RECOVERY_CODE_1, self::RECOVERY_CODE_2, self::RECOVERY_CODE_3];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_two_factor_authentication_component_renders(): void
    {
        Livewire::test(TwoFactorAuthentication::class)
            ->assertOk();
    }

    public function test_2fa_challenge_page_requires_valid_session(): void
    {
        // Try to access challenge page without session
        $response = $this->get(route('two-factor.login'));
        $response->assertRedirect(route('login'));
    }

    public function test_2fa_challenge_page_shows_with_valid_session(): void
    {
        $user = $this->createUser();

        // Simulate login session
        Session::put('login.id', $user->id);

        $response = $this->get(route('two-factor.login'));
        $response->assertStatus(200);
        $response->assertSee('Two Factor Authentication');
    }

    public function test_successful_totp_verification_logs_user_in(): void
    {
        $user = $this->createUserWith2FA();
        $this->mock2FAProvider(true, self::TEST_SECRET, self::VALID_TOTP_CODE);
        $this->setLoginSession($user);

        $response = $this->post(route('two-factor.challenge'), [
            'code' => self::VALID_TOTP_CODE,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertNull(Session::get('login.id')); // Session should be cleared
    }

    public function test_invalid_totp_code_shows_error(): void
    {
        $user = $this->createUserWith2FA();
        $this->mock2FAProvider(false);
        $this->setLoginSession($user);

        $response = $this->post(route('two-factor.challenge'), [
            'code' => self::INVALID_CODE,
        ]);

        // Validation errors redirect back with errors
        $response->assertStatus(302);
        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_invalid_recovery_code_shows_error(): void
    {
        $user = $this->createUserWith2FA();
        $this->setLoginSession($user);

        $response = $this->post(route('two-factor.challenge'), [
            'recovery_code' => self::INVALID_RECOVERY_CODE,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('recovery_code');
        $this->assertGuest();
    }

  public function test_empty_authentication_fields_show_error(): void
    {
        $user = $this->createUserWith2FA();
        $this->setLoginSession($user);

        $response = $this->post(route('two-factor.challenge'), []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_totp_code_must_be_6_digits(): void
    {
        $user = $this->createUserWith2FA();
        $this->setLoginSession($user);

        $response = $this->post(route('two-factor.challenge'), [
            'code' => self::INVALID_SHORT_CODE,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_session_regeneration_on_successful_auth(): void
    {
        $user = $this->createUser();
        $user->assignRole('member');

        $user->forceFill([
            'two_factor_secret' => encrypt(self::TEST_SECRET_ALT),
            'two_factor_recovery_codes' => encrypt(json_encode(self::RECOVERY_CODES)),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->setLoginSession($user);
        $oldSessionId = Session::getId();

        $response = $this->post(route('two-factor.challenge'), [
            'recovery_code' => self::RECOVERY_CODE_1,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        // Session should be regenerated (new session ID)
        $this->assertNotEquals($oldSessionId, Session::getId());
    }

  public function test_used_recovery_code_is_removed(): void
    {
        $user = $this->createUser();

        $user->forceFill([
            'two_factor_secret' => encrypt(self::TEST_SECRET_ALT),
            'two_factor_recovery_codes' => encrypt(json_encode(self::RECOVERY_CODES_THREE)),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->setLoginSession($user);

        $this->post(route('two-factor.challenge'), [
            'recovery_code' => self::RECOVERY_CODE_2,
        ]);

        $user->refresh();
        $remainingCodes = json_decode(decrypt((string)$user->two_factor_recovery_codes), true);

        $this->assertCount(2, $remainingCodes);
        $this->assertContains(self::RECOVERY_CODE_1, $remainingCodes);
        $this->assertContains(self::RECOVERY_CODE_3, $remainingCodes);
        $this->assertNotContains(self::RECOVERY_CODE_2, $remainingCodes);
    }

    public function test_user_without_2fa_secret_cannot_access_challenge(): void
    {
        $user = $this->createUser();
        $this->setLoginSession($user);

        $response = $this->post(route('two-factor.challenge'), [
            'code' => self::VALID_TOTP_CODE,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_challenge_without_session_redirects_to_login(): void
    {
        $response = $this->post(route('two-factor.challenge'), [
            'code' => self::VALID_TOTP_CODE,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

   public function test_multiple_failed_attempts_maintain_guest_status(): void
   {
       $user = $this->createUser();
       $user->forceFill([
           'two_factor_secret' => encrypt(self::TEST_SECRET_ALT),
           'two_factor_recovery_codes' => encrypt(json_encode(self::RECOVERY_CODES)),
           'two_factor_confirmed_at' => now(),
       ])->save();

       $this->setLoginSession($user);

       // Multiple failed attempts
       for ($i = 0; $i < 3; $i++) {
           $response = $this->post(route('two-factor.challenge'), [
               'code' => self::INVALID_CODE . $i,
           ]);

           $response->assertStatus(302);
           $this->assertGuest();
       }
   }

    public function test_recovery_codes_are_properly_reindexed_after_removal(): void
    {
        $user = User::factory()->create();

        $user->forceFill([
            'two_factor_secret' => encrypt(self::TEST_SECRET_ALT),
            'two_factor_recovery_codes' => encrypt(json_encode(self::RECOVERY_CODES_THREE)),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->setLoginSession($user);

        // Use the middle recovery code
        $this->post(route('two-factor.challenge'), [
            'recovery_code' => self::RECOVERY_CODE_2,
        ]);

        $user->refresh();
        $remainingCodes = json_decode(decrypt((string)$user->two_factor_recovery_codes), true);

        // Should have 2 codes remaining, properly reindexed
        $this->assertCount(2, $remainingCodes);
        $this->assertContains(self::RECOVERY_CODE_1, $remainingCodes);
        $this->assertContains(self::RECOVERY_CODE_3, $remainingCodes);
    }

    private function createUserWith2FA(array $recoveryCodes = self::RECOVERY_CODES): User
    {
        $user = $this->createUser();
        $user->forceFill([
            'two_factor_secret' => encrypt(self::TEST_SECRET),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => now(),
        ])->save();
        return $user;
    }

    private function mock2FAProvider(bool $verifyResult = true, ?string $expectedSecret = null, ?string $expectedCode = null): void
    {
        $this->mock(TwoFactorAuthenticationProvider::class, function ($mock) use ($verifyResult, $expectedSecret, $expectedCode) {
            if ($expectedSecret && $expectedCode) {
                $mock->shouldReceive('verify')
                    ->with($expectedSecret, $expectedCode)
                    ->andReturn($verifyResult);
            } else {
                $mock->shouldReceive('verify')->andReturn($verifyResult);
            }
        });
    }

    private function setLoginSession(User $user): void
    {
        Session::put('login.id', $user->id);
    }
}
