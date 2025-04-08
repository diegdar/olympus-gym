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
        $this->fee = $request->input('fee', '');
        $this->registerMessage = $request->input('registerMessage', '');
    }

    public function register(): void
    {
        $validated = $this->validate($this->rules(), $this->messages());

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'fee' => ['required', 'in:monthly,quarterly,yearly'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'privacy' => ['required', 'accepted'],
        ];
    }

    protected function messages(): array
    {
        return [
            // name
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre debe tener menos de 255 caracteres.',     
            // email   
            'email.required' => 'El email es obligatorio.',
            'email.string' => 'El email debe ser una cadena de texto.',
            'email.lowercase' => 'El email debe estar en minúsculas.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.max' => 'El email debe tener menos de 255 caracteres.',
            'email.unique' => 'El email ya está registrado.', 
            // fee
            'fee.required' => 'Debes seleccionar una cuota.',
            'fee.in' => 'La cuota seleccionada no es válida.',
            // password
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            // privacy
            'privacy.required' => 'Debes aceptar la política de privacidad.',
            'privacy.accepted' => 'Debes aceptar la política de privacidad.',
        ];
    }
}
