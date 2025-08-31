<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StoreSubscriptionService
{
    public function __invoke(User $user, int $subscriptionId): bool
    {
        $newSubscription = $this->resolveSubscription($subscriptionId);
        $currentSubscription = $this->getCurrentSubscription($user);

        if ($currentSubscription && $currentSubscription->id === $newSubscription->id) {
            return false;
        }

        if ($currentSubscription) {
            $this->deactivateSubscription($user, $currentSubscription);
        }

        [$startDate, $endDate] = $this->computeDates($currentSubscription, $newSubscription);
        $this->attachOrReactivate($user, $newSubscription, $startDate, $endDate);

        return true;
    }

    private function resolveSubscription(int $subscriptionId): Subscription
    {
        return Subscription::findOrFail($subscriptionId);
    }

    private function getCurrentSubscription(User $user): ?Subscription
    {
        return $user->subscriptions()
            ->wherePivot('status', 'active')
            ->orderBy('subscription_user.updated_at', 'desc')
            ->first();
    }

    private function deactivateSubscription(User $user, Subscription $current): void
    {
        DB::table('subscription_user')
            ->where('user_id', $user->id)
            ->where('subscription_id', $current->id)
            ->where('status', 'active')
            ->update([
                'status' => 'inactive',
                'end_date' => Carbon::now()->format('Y-m-d'),
                'updated_at' => Carbon::now(),
            ]);
    }

    /**
     * @return array{\Carbon\Carbon, \Carbon\Carbon}
     */
    private function computeDates(?Subscription $currentSubscription, Subscription $newSubscription): array
    {
        $startDate = $currentSubscription
            ? Carbon::parse($currentSubscription->pivot->end_date)->addDay()
            : now();

        $endDate = (clone $startDate)->addMonths($newSubscription->duration);

        return [$startDate, $endDate];
    }

    private function attachOrReactivate(User $user, Subscription $newSubscription, Carbon $startDate, Carbon $endDate): void
    {
        $existing = $this->getExistingPivot($user, $newSubscription);

        $this->attachNewPivot($user, $newSubscription, $startDate, $endDate);
    }

    private function getExistingPivot(User $user, Subscription $newSubscription): ?object
    {
        return DB::table('subscription_user')
            ->where('user_id', $user->id)
            ->where('subscription_id', $newSubscription->id)
            ->first();
    }

    private function attachNewPivot(User $user, Subscription $newSubscription, Carbon $startDate, Carbon $endDate): void
    {
        $user->subscriptions()->attach($newSubscription->id, $this->pivotData($startDate, $endDate));
    }

    private function pivotData(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'payment_date' => Carbon::now()->toDateTimeString(),
            'status' => 'active',
        ];
    }
}
