<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear super-admin y admin
        $this->createUser([
            'name' => env('SUPER_ADMIN_NAME'),
            'email' => env('SUPER_ADMIN_EMAIL'),
            'password' => Hash::make(env('SUPER_ADMIN_PASSWORD')),
            'birth_date' => now()->subYears(38)->toDateString(),
        ], 'super-admin');

        $this->createUser([
            'name' => env('ADMIN_NAME'),
            'email' => env('ADMIN_EMAIL'),
            'password' => Hash::make(env('ADMIN_PASSWORD')),
            'birth_date' => now()->subYears(34)->toDateString(),
        ], 'admin');

        // Crear un member con suscripción mensual
        $member = $this->createUser([
            'name' => env('MEMBER_NAME'),
            'email' => env('MEMBER_EMAIL'),
            'password' => Hash::make(env('MEMBER_PASSWORD')),
            'birth_date' => now()->subYears(29)->toDateString(),
        ], 'member');

        $this->attachSubscription($member, 'quarterly');

        // Crear otros miembros
        $this->createMultipleMembers(15);
    }

    /**
     * Create a user with the given attributes and assign a role.
     */
    private function createUser(array $attributes, string $role): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    /**
     * Attach a subscription (by fee key) to the given user if it exists.
     */
    private function attachSubscription(User $user, string $fee): void
    {
        $subscription = Subscription::where('fee', operator: $fee)->first();
        if (! $subscription) {
            return;
        }

        $startDate = Carbon::now();
        $endDate = $startDate->copy()->addMonths($subscription->duration);

        $user->subscriptions()->attach($subscription->id, [
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'payment_date' => Carbon::now(),
            'status'       => 'active',
        ]);
    }

    /**
     * Create multiple member users and assign them the 'member' role.
     */
    private function createMultipleMembers(int $count): void
    {
        User::factory($count)->create()
            ->each(function ($user) {
                $user->assignRole('member');
        });
    }
}
