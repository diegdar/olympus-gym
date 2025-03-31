<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Http\Request;


#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $fee = '';

    public string $password_confirmation = '';

    public bool $privacy = false;

    public string $registerMessage;

    public function mount(Request $request): void
    {
        if ($request->has('fee')) {
            $this->fee = $request->fee;
        }
        if ($request->has('registerMessage')) {
            $this->registerMessage = $request->registerMessage;
        }        
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'fee' => ['required', 'in:monthly,quarterly,yearly'], 
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'privacy' => ['required', 'accepted'],
        ], [
            'privacy.accepted' => 'Debes aceptar la política de privacidad.',
            'fee.required' => 'Debes seleccionar una cuota.',
            'fee.in' => 'Debes seleccionar una cuota.',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
