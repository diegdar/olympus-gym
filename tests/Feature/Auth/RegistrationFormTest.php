<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_the_register_component()
    {
        $response = $this->get(route('register'))->assertSeeLivewire(Register::class);

        $response->assertStatus(200);
    }

    public function test_it_requires_valid_inputs()
    {
        Livewire::test(Register::class)
            ->set('name', '')
            ->set('email', 'invalid-email')
            ->set('password', 'short')
            ->set('password_confirmation', 'mismatch')
            ->set('privacy', false)
            ->call('register')
            ->assertHasErrors([
                'name' => 'required',
                'email' => 'email',
                'password' => 'confirmed',
                'privacy' => 'accepted',
            ]);
    }

    public function test_it_registers_a_user_successfully()
    {
        Event::fake();// disable the events and pretends they were triggered

        Livewire::test(Register::class)
            ->set('name', 'Joe Doe')
            ->set('email', 'joe.doe@example.com')
            ->set('fee', 'monthly')
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'SecurePassword123!')
            ->set('privacy', true)
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'name' => 'Joe Doe',
            'email' => 'joe.doe@example.com',
        ]);
        $this->assertAuthenticated();

        $user = User::where('email', 'joe.doe@example.com')->first();
        $this->assertTrue(Hash::check('SecurePassword123!', $user->password));

        Event::assertDispatched(Registered::class);// check if the event was dispatched 
    }
}
