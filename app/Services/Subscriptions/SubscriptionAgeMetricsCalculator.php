<?php
declare(strict_types=1);

namespace App\Services\Subscriptions;

use App\Models\User;
use Carbon\Carbon;

class SubscriptionAgeMetricsCalculator
{
    public function __invoke(): array
    {
        $users = User::query()
            ->whereNotNull('birth_date')
            ->whereHas('subscriptions', fn($q) => $q->where('status','active'))
            ->get(['birth_date']);

        if ($users->isEmpty()) {
            return [
                'count_active_with_birth_date' => 0,
                'average_age' => null,
                'min_age' => null,
                'max_age' => null,
                'median_age' => null,
                'rows' => [],
            ];
        }

        $ages = $users->map(fn($u) => Carbon::parse($u->birth_date)->age)->sort()->values();
        $count = $ages->count();

        // Aggregate core stats (kept for potential other UI uses)
        $average = round($ages->avg(), 2);
        $min = $ages->first();
        $max = $ages->last();
        $median = $this->median($ages->all());

        // Requested buckets: 14-20, 21-35, 36-45, 46-60, >60
        $buckets = [
            ['label' => '14 - 20', 'min' => 14, 'max' => 20],
            ['label' => '21 - 35', 'min' => 21, 'max' => 35],
            ['label' => '36 - 45', 'min' => 36, 'max' => 45],
            ['label' => '46 - 60', 'min' => 46, 'max' => 60],
            ['label' => '61+',     'min' => 61, 'max' => null],
        ];

        $rows = collect($buckets)->map(function ($b) use ($ages, $count) {
            $bucketCount = $ages->filter(function ($age) use ($b) {
                $minOk = $age >= $b['min'];
                $maxOk = $b['max'] === null ? true : $age <= $b['max'];
                return $minOk && $maxOk;
            })->count();
            $percentage = $count > 0 ? round(($bucketCount / $count) * 100, 2) : 0;
            return [
                'range' => $b['label'],
                'count' => $bucketCount,
                'percentage' => $percentage,
            ];
        })->values();

        return [
            'count_active_with_birth_date' => $count,
            'average_age' => $average,
            'min_age' => $min,
            'max_age' => $max,
            'median_age' => $median,
            'rows' => $rows,
        ];
    }

    private function median(array $values): float|int|null
    {
        $n = count($values);
        if ($n === 0) return null;
        $middle = intdiv($n, 2);
        if ($n % 2) {
            return $values[$middle];
        }
        return round(($values[$middle - 1] + $values[$middle]) / 2, 2);
    }
}
