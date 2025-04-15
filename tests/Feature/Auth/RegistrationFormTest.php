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

class RegistrationFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();        
    }

    private function getUserData(): array
    {
        return User::factory([
                'name' => 'Joe Doe',
                'email' => 'joe.doe@example.com',  
                'fee' => 'monthly',              
            ])->raw();
    }    

    public function test_it_registers_a_user_successfully()
    {
        Event::fake();// disable the events and pretends they were triggered

        $this->post(route('user.register'), $this->getUserData());

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
    // public function test_it_registers_a_user_successfully()
    // {
    //     Event::fake();// disable the events and pretends they were triggered


    //     Livewire::test(Register::class)
    //         ->set('name', 'Joe Doe')
    //         ->set('email', 'joe.doe@example.com')
    //         ->set('fee', 'monthly')
    //         ->set('password', 'SecurePassword123!')
    //         ->set('password_confirmation', 'SecurePassword123!')
    //         ->set('privacy', true)
    //         ->set('role', 'member')
    //         ->call('register')
    //         ->assertHasNoErrors()
    //         ->assertRedirect(route('dashboard', absolute: false));

    //     $this->assertDatabaseHas('users', [
    //         'name' => 'Joe Doe',
    //         'email' => 'joe.doe@example.com',
    //     ]);

    //     $subscription = Subscription::where('fee', 'monthly')->first();
    //     $user = User::where('email', 'joe.doe@example.com')->first();

    //     $this->assertDatabaseHas('subscription_user', [
    //         'user_id' => $user->id,
    //         'subscription_id' => $subscription->id,
    //     ]);
    //     $this->assertAuthenticated();

    //     Event::assertDispatched(Registered::class);// check if the event was dispatched 
    // }    

    public function test_it_requires_valid_inputs()
    {
        Livewire::test(Register::class)
            ->set('name', '')
            ->set('email', 'invalid-email')
            ->set('fee', 'not-a-fee')
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


}
