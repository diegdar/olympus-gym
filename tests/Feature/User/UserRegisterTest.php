<?php

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_the_register_component()
    {
        $this->get(route('register'))->assertSeeLivewire(Register::class);
    }

    public function test_it_requires_valid_inputs()
    {
        Livewire::test(Register::class)
            ->set('name', '')
            ->set('email', 'invalid-email')
            ->set('password', 'short')
            ->set('password_confirmation', 'mismatch')
            ->call('register')
            ->assertHasErrors([
                'name' => 'required',
                'email' => 'email',
                'password' => 'confirmed',
            ]);
    }

    public function test_it_registers_a_user_successfully()
    {
        Event::fake();// disable the events and pretends they were triggered

        Livewire::test(Register::class)
            ->set('name', 'Joe Doe')
            ->set('email', 'joe.doe@example.com')
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'SecurePassword123!')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'name' => 'Joe Doe',
            'email' => 'joe.doe@example.com',
        ]);

        $user = User::where('email', 'joe.doe@example.com')->first();
        $this->assertTrue(Hash::check('SecurePassword123!', $user->password));

        Event::assertDispatched(Registered::class);// check if the event was dispatched 
    }

    public function test_it_logs_in_the_user_after_registration()
    {
        Livewire::test(Register::class)
            ->set('name', 'Joe Doe')
            ->set('email', 'joe.doe@example.com')
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'SecurePassword123!')
            ->call('register');

        $this->assertAuthenticated();
        $this->assertEquals('joe.doe@example.com', Auth::user()->email);
    }

    public function test_it_redirects_to_dashboard_after_registration()
    {
        Livewire::test(Register::class)
            ->set('name', 'Joe Doe')
            ->set('email', 'joe.doe@example.com')
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'SecurePassword123!')
            ->call('register')
            ->assertRedirect(route('dashboard'));
    }
}



