<?php
declare(strict_types=1);

namespace Tests\Feature\Users;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class ShowUserSubscriptionTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    private const ROUTE = 'member.subscription';
    private const PAGE_TITLE = 'Mi suscripción';
    private const NOT_ASSIGNED = 'No asignada';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function assertCommonPageElements($response, $user): void
    {
        $response->assertStatus(200)
                ->assertSee(self::PAGE_TITLE)
                ->assertSee($user->name);
    }

    public function test_guest_cannot_view_subscription(): void
    {
        $this->get(route(self::ROUTE))->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_their_subscription(): void
    {
        [$user, $subscription, $startDate, $endDate] = $this->createSubscription();

        $response = $this->actingAs($user)->get(route(self::ROUTE));

        $this->assertCommonPageElements($response, $user);
        $response->assertSee($subscription->fee_translated)
                ->assertSee(Carbon::parse($startDate)
                    ->translatedFormat('j \d\e F \d\e Y'))
                ->assertSee(Carbon::parse($endDate)
                    ->translatedFormat('j \d\e F \d\e Y'));
    }

    public function test_user_with_no_subscription_sees_not_assigned_message(): void
    {
        $user = $this->createUserAndSignIn('member');

        $response = $this->actingAs($user)->get(route(self::ROUTE));

        $this->assertCommonPageElements($response, $user);
        $response->assertSee(self::NOT_ASSIGNED);
    }

    public function test_user_with_inactive_subscription_sees_not_assigned_message(): void
    {
        [$user] = $this->createSubscription('inactive');

        $response = $this->actingAs($user)->get(route(self::ROUTE));

        $this->assertCommonPageElements($response, $user);
        $response->assertSee(self::NOT_ASSIGNED);
    }
}
