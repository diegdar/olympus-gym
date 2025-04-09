<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SubscriptionUserSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = Subscription::all();
        $users = User::all();

        foreach ($users as $user) {
            $subscription = $this->getUniqueSubscriptionForUser($user, $subscriptions);
            $startDate = fake()->date('Y-m-d');
            $endDate = Carbon::parse($startDate)->addMonths($subscription->duration);

            $user->subscriptions()->attach($subscription->id, [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'payment_date' => fake()->dateTimeBetween($startDate, '-3 days')->format('Y-m-d'),
            ]);
        }
    }

    private function getUniqueSubscriptionForUser(User $user, $subscriptions): Subscription
    {
        do {
            $subscription = $subscriptions->random();
        } while (
            $user->subscriptions()->where('subscription_id', $subscription->id)->exists()
        );

        return $subscription;
    }
}
