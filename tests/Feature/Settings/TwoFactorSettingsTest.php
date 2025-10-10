<?php
declare(strict_types=1);

namespace Tests\Feature\Settings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;
use Database\Seeders\RoleSeeder;
use Livewire\Livewire;
use App\Livewire\Settings\TwoFactorAuthentication;
use App\Models\User;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

class TwoFactorSettingsTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_two_factor_settings_component_renders():void
    {
        Livewire::test(TwoFactorAuthentication::class)
            ->assertOk();
    }

    public function test_user_can_enable_two_factor_authentication(): void
    {
        $user = User::factory()->create();
        $user->assignRole('member');

        // Mock the 2FA provider
        $this->mock(TwoFactorAuthenticationProvider::class, function ($mock) {
            $mock->shouldReceive('generateSecretKey')
                ->andReturn('JBSWY3DPEHPK3PXP');
            $mock->shouldReceive('verify')
                ->andReturn(true);
            $mock->shouldReceive('qrCodeUrl')
                ->andReturn('otpauth://totp/Olympus%20Gym:test@example.com?secret=JBSWY3DPEHPK3PXP&issuer=Olympus%20Gym');
        });

        Livewire::actingAs($user)
            ->test(TwoFactorAuthentication::class)
            ->assertOk()
            ->assertSet('showingQrCode', false)
            ->assertSet('showingConfirmation', false)
            ->assertSet('showingRecoveryCodes', false)
            ->assertSet('enabled', false)

            // Enable 2FA
            ->call('enableTwoFactorAuthentication')
            ->assertSet('showingQrCode', true)
            ->assertSet('showingConfirmation', true)
            ->assertSet('enabled', true)
            ->assertNotSet('qrCode', '')

            // Confirm with valid code
            ->set('code', '123456')
            ->call('confirmTwoFactorAuthentication')
            ->assertSet('showingQrCode', false)
            ->assertSet('showingConfirmation', false)
            ->assertSet('showingRecoveryCodes', true)
            ->assertHasNoErrors();

        // Verify user has 2FA enabled and confirmed
        $user->refresh();
        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_confirmed_at);
        $this->assertNotNull($user->two_factor_recovery_codes);
    }


}
