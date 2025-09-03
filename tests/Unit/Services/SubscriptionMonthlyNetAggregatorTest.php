<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Services\Subscriptions\SubscriptionMonthlyNetAggregator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionMonthlyNetAggregatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_net_counts(): void
    {
        $sub = Subscription::create(['fee' => 'monthly','description' => 'm','price' => 10,'duration' => 1]);
        $year = now()->year;

        foreach (range(1,2) as $_) {
            $u = User::factory()->create();
            $start = now()->startOfYear();
            $u->subscriptions()->attach($sub->id,[
                'start_date' => $start,
                'end_date' => $start->copy()->addMonths(2),
                'payment_date' => $start,
                'status' => 'active',
            ]);
        }

        $u3 = User::factory()->create();
        $startFeb = now()->startOfYear()->addMonth();
        $u3->subscriptions()->attach($sub->id,[
            'start_date' => $startFeb,
            'end_date' => $startFeb->copy()->addDays(10),
            'payment_date' => $startFeb,
            'status' => 'active',
        ]);

        $service = new SubscriptionMonthlyNetAggregator();
        $result = $service($year);

        $data = collect($result['data'])->keyBy('month');
        $enero = $data['01'];
        $feb = $data['02'];

        $this->assertEquals(2, $enero['signups']);
        $this->assertEquals(0, $enero['cancellations']);
        $this->assertEquals(2, $enero['net']);

        $this->assertEquals(1, $feb['signups']);
        $this->assertEquals(1, $feb['cancellations']);
        $this->assertEquals(0, $feb['net']);
    }
}
