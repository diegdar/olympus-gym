<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Services\Subscriptions\SubscriptionPercentagesCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionPercentagesCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_percentages_correctly(): void
    {
        Subscription::create(['fee' => 'monthly','description' => 'm','price' => 10,'duration' => 1]);
        Subscription::create(['fee' => 'quarterly','description' => 'q','price' => 25,'duration' => 3]);

        $monthly = Subscription::where('fee','monthly')->first();
        $quarterly = Subscription::where('fee','quarterly')->first();

        $u1 = User::factory()->create();
        $u2 = User::factory()->create();
        $u3 = User::factory()->create();

        foreach ([[$u1,$monthly],[$u2,$monthly],[$u3,$quarterly]] as [$user,$sub]) {
            $start = now();
            $user->subscriptions()->attach($sub->id,[
                'start_date' => $start,
                'end_date' => $start->copy()->addMonth(),
                'payment_date' => $start,
                'status' => 'active',
            ]);
        }

        $service = new SubscriptionPercentagesCalculator();
        $result = $service();

        $this->assertEquals(3, $result['total_active_users']);
        $data = collect($result['data']);
        $monthlyRow = $data->firstWhere('fee','monthly');
        $quarterlyRow = $data->firstWhere('fee','quarterly');

        $this->assertEquals(2, $monthlyRow['users']);
        $this->assertEquals(1, $quarterlyRow['users']);
        $this->assertEquals(round((2/3)*100,2), $monthlyRow['percentage']);
        $this->assertEquals(round((1/3)*100,2), $quarterlyRow['percentage']);
    }
}
