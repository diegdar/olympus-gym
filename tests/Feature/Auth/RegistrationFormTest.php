<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SubscriptionSeeder;

class RegistrationFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_it_renders_the_register_component()
    {
        $response = $this->get(route('register'))->assertSeeLivewire(Register::class);

        $response->assertStatus(200);
    }

    public function test_it_registers_a_user_successfully()
    {
        $this->seed([RoleSeeder::class, SubscriptionSeeder::class]);

        Event::fake();// disable the events and pretends they were triggered
        Livewire::test(Register::class)
            ->set('name', 'Joe Doe')
            ->set('email', 'joe.doe@example.com')
            ->set('fee', 'monthly')
            ->set('birth_date', now()->subYears(25)->toDateString())
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'SecurePassword123!')
            ->set('privacy', true)
            ->set('role', 'member')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'name' => 'Joe Doe',
            'email' => 'joe.doe@example.com',
        ]);

        $subscription = Subscription::where('fee', 'monthly')->first();
        $user = User::where('email', 'joe.doe@example.com')->first();

        $this->assertDatabaseHas('subscription_user', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
        ]);
        $this->assertAuthenticated();

        Event::assertDispatched(Registered::class);// check if the event was dispatched 
    }    

    public function test_it_requires_valid_inputs()
    {
        Livewire::test(Register::class)
            ->set('name', '')
            ->set('email', 'invalid-email')
            ->set('fee', 'not-a-fee')
            ->set('birth_date', '')
            ->set('password', 'short')
            ->set('password_confirmation', 'mismatch')
            ->set('privacy', false)
            ->set('role', 'not-a-role')
            ->call('register')
            ->assertHasErrors([
                'name' => 'required',
                'email' => 'email',
                'password' => 'confirmed',
                'privacy' => 'accepted',
            ]);
    }

    public function test_it_rejects_user_younger_than_14()
    {
        Livewire::test(Register::class)
            ->set('name', 'Young User')
            ->set('email', 'young.user@example.com')
            ->set('fee', 'monthly')
            ->set('birth_date', now()->subYears(10)->toDateString())
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'SecurePassword123!')
            ->set('privacy', true)
            ->set('role', 'member')
            ->call('register')
            ->assertHasErrors(['birth_date' => 'before_or_equal']);
    }


}
