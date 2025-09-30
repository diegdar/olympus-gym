<?php
declare(strict_types=1);

namespace Tests\Feature\Users;

use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SubscriptionSeeder;

class ChangeSubscriptionTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, SubscriptionSeeder::class]);
    }

    public function test_member_can_change_subscription(): void
    {
        [$user] = $this->createSubscription();
        $newSubscription = Subscription::where('fee', 'yearly')->first();

        $this->actingAs($user)
             ->from(route('member.subscription'))
             ->put(route('member.subscription.update'), ['subscription_id' => $newSubscription->id])
             ->assertRedirect(route('member.subscription'))
             ->assertSessionHas('msg');

        $this->assertDatabaseHas('subscription_user', [
            'user_id' => $user->id,
            'subscription_id' => $newSubscription->id,
        ]);
    }

    public function test_cannot_change_to_same_subscription(): void
    {
        [$user] = $this->createSubscription();
        $subscription = $user->subscriptions()->first();

        $this->actingAs($user)
             ->from(route('member.subscription'))
             ->put(route('member.subscription.update'), ['subscription_id' => $subscription->id])
             ->assertRedirect(route('member.subscription'))
             ->assertSessionHasErrors(['subscription_id' => 'Ya estabas suscrito en esta cuota, elige otra.']);

        $this->assertDatabaseHas('subscription_user', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
        ]);
    }

    public function test_validation_requires_subscription_id(): void
    {
        $user = $this->createUserAndAssignRole('member');

        $this->actingAs($user)
             ->from(route('member.subscription'))
             ->put(route('member.subscription.update'), [])
             ->assertSessionHasErrors('subscription_id', 'Debes elegir una suscripcion');
    }

    public function test_previous_subscription_becomes_inactive_on_change(): void
    {
        /**
         * Sets the current time to the present moment for testing purposes
         * This is useful for time-sensitive tests where we need to simulate the current time
         */
        $this->travelTo(now());
        [$user, $oldSubscription] = $this->createSubscription('active');
        $newSubscription = Subscription::where('fee', 'yearly')->first();

        $this->actingAs($user)
             ->from(route('member.subscription'))
             ->put(route('member.subscription.update'), ['subscription_id' => $newSubscription->id])
             ->assertRedirect(route('member.subscription'));

        $this->assertDatabaseHas('subscription_user', [
            'user_id' => $user->id,
            'subscription_id' => $oldSubscription->id,
            'status' => 'inactive',
            'end_date' => now()->format('Y-m-d'),
        ]);
    }
}