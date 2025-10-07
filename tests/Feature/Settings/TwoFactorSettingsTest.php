<?php
declare(strict_types=1);

namespace Tests\Feature\Settings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;
use Database\Seeders\RoleSeeder;
use Livewire\Livewire;
use App\Livewire\Settings\TwoFactorAuthentication;
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


}
