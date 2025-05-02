<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class SubscriptionUserSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = Subscription::all();
        $users = User::role('member')->get();

        foreach ($users as $user) {
            $subscription = $this->getAnUniqueSubscriptionForUser($user, $subscriptions);
            $startDate = fake()->dateTimeBetween('today', '+30 days');
            $endDate = Carbon::parse($startDate)->addMonths($subscription->duration);
            $paymentDate = fake()->dateTimeBetween( '-7 days', $startDate);

            $user->subscriptions()->attach($subscription->id, [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'payment_date' => $paymentDate,
            ]);
        }
    }

    private function getAnUniqueSubscriptionForUser(User $user, Collection $subscriptions): Subscription
    {
        do {
            $subscription = $subscriptions->random();
        } while (
            $user->subscriptions()->where('subscription_id', $subscription->id)->exists()
        );

        return $subscription;
    }
}
