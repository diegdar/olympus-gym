<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Http\Requests\CreateUserFormRequest;
use App\Services\CreateUserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    Hash,
};
use Livewire\{
    Attributes\Layout,
    Component,
};

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';   
    public string $password = '';
    public string $fee = '';
    public string $password_confirmation = '';
    public ?string $role = 'member';
    public bool $privacy = false;
    public string $registerMessage;

    public function mount(Request $request): void
    {
        $this->fee = $request->input('fee', '');
        $this->registerMessage = $request->input('registerMessage', '');
    }

    public function rules(): array
    {
        return (new CreateUserFormRequest())->rules();
    }


    public function register(CreateUserService $createUserService): void
    {
        $validated = $this->validate();
    
        $validated['password'] = Hash::make($validated['password']);
    
        $user = $createUserService($validated);
        Auth::login($user);        
    
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

}
