<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Services\Subscriptions\SubscriptionAgeMetricsCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionAgeMetricsCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_metrics_distribution_buckets()
    {
        $sub = Subscription::create(['fee' => 'monthly','description' => 'm','price' => 10,'duration' => 1]);
        // Ages: 18, 22, 40, 55, 65
        $u1 = User::factory()->create(['birth_date' => now()->subYears(18)->toDateString()]);
        $u2 = User::factory()->create(['birth_date' => now()->subYears(22)->toDateString()]);
        $u3 = User::factory()->create(['birth_date' => now()->subYears(40)->toDateString()]);
        $u4 = User::factory()->create(['birth_date' => now()->subYears(55)->toDateString()]);
        $u5 = User::factory()->create(['birth_date' => now()->subYears(65)->toDateString()]);
        foreach ([$u1,$u2,$u3,$u4,$u5] as $u) {
            $u->subscriptions()->attach($sub->id, [
                'start_date' => now(), 'end_date' => now()->addMonths(1), 'payment_date' => now(), 'status' => 'active'
            ]);
        }

        $service = new SubscriptionAgeMetricsCalculator();
        $data = $service();

        $this->assertEquals(5, $data['count_active_with_birth_date']);
        $this->assertCount(5, $data['rows']);
        $ranges = $data['rows'];
        $map = collect($ranges)->keyBy('range');
        $this->assertEquals(1, $map['14 - 20']['count']); // 18
        $this->assertEquals(1, $map['21 - 35']['count']); // 22
        $this->assertEquals(1, $map['36 - 45']['count']); // 40
        $this->assertEquals(1, $map['46 - 60']['count']); // 55
        $this->assertEquals(1, $map['61+']['count']);     // 65
        $this->assertEquals(100.0, round($ranges->sum('percentage'),2));
    }
}
