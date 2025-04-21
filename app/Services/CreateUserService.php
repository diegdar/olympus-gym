<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\{
    Subscription,
    User,
};
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class CreateUserService
{
    public function __invoke(array $validated): User
    {
        $validated['password'] = Hash::make($validated['password']);
        return $this->createUser($validated);
    }

    public function createUser(array $validated): User
    {
        return DB::transaction(function () use ($validated) {
            $user = User::create($validated);
            $user->assignRole($validated['role'] ?? 'member');

            if(isset($validated['fee']) ){
                $this->subscribeOrFail($user, $validated['fee']);
            }
    
            event(new Registered($user));
    
            return $user;
        });
    }
    
    private function subscribeOrFail(User $user, string $subscriptionValue): void
    {
        $subscription = Subscription::where('fee', $subscriptionValue)->first();
        
        if($user->subscribeTo($subscription)) {
            return;
        }

        throw new \RuntimeException('Ocurrio un error al tratar de suscribir al usuario');            
    }  

}
