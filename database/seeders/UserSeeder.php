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
        $this->createUser([
            'name' => 'diego chacon',
            'email' => 'diego_chacon@superadmin.com',
            'password' => Hash::make('PassNix$123'),
            'birth_date' => now()->subYears(38)->toDateString(),
        ], 'super-admin');

        $this->createUser([
            'name' => 'luis guillermo',
            'email' => 'luis_guillermo@admin.com',
            'password' => Hash::make('PassNix$123'),
            'birth_date' => now()->subYears(34)->toDateString(),
        ], 'admin');

        $member = $this->createUser([
            'name' => 'raul prieto',
            'email' => 'raul_prieto@socio.com',
            'password' => Hash::make('PassNix$123'),
            'birth_date' => now()->subYears(29)->toDateString(),
        ], 'member');

        $this->attachSubscription($member, 'quarterly');

    // create less users in testing environment
    $this->createMultipleMembers(app()->environment('testing') ? 3 : 15);
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
