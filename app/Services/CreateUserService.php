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
    
            if (!$this->subscribeOrFail($user, $validated['fee'])) {
                throw new \RuntimeException('El usuario ya estaba suscripto');
            }
    
            event(new Registered($user));
    
            return $user;
        });
    }
    
    private function subscribeOrFail(User $user, string $subscriptionValue): bool
    {
        $subscription = Subscription::where('fee', $subscriptionValue)->first();
    
        return $user->subscribeTo($subscription); 
    }  

}
