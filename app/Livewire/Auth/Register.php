<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\{
    Subscription,
    User,
};
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    DB,
    Hash,
};
use Illuminate\Validation\Rules;
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
    public bool $privacy = false;
    public string $registerMessage;

    public function mount(Request $request): void
    {
        $this->fee = $request->input('fee', '');
        $this->registerMessage = $request->input('registerMessage', '');
    }


    public function register(): void
    {
        $validated = $this->validate($this->rules());
    
        $validated['password'] = Hash::make($validated['password']);
    
        DB::transaction(function () use ($validated) {
            $user = User::create($validated);
    
            $this->subscribeOrFail($user, $validated['fee']);
    
            event(new Registered($user));
            Auth::login($user);
        });
    
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
    
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'fee' => ['required', 'string', 'exists:subscriptions,fee'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'privacy' => ['required', 'accepted'],
        ];
    }

    private function subscribeOrFail(User $user, string $subscriptionValue)
    {
        $subscription = Subscription::where('fee', $subscriptionValue)->first();

        if(!$user->subscribeTo($subscription)) {
            return redirect()->route('register', ['registerMessage'=>'Error al suscribirse']);
        }        
    }
}
