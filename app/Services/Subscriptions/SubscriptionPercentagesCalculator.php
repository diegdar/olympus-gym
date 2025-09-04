<?php
declare(strict_types=1);

namespace App\Services\Subscriptions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\User;
use Carbon\Carbon;

/**
 * Calculates percentage of active users per subscription fee.
 */
class SubscriptionPercentagesCalculator
{
    public function __invoke(): array
    {
        $totalActiveUsers = $this->countDistinctActiveUsers();

        /** @var Collection<int,array{fee:string,users:int}> $raw */
        $raw = DB::table('subscription_user as su')
            ->join('subscriptions as s', 's.id', '=', 'su.subscription_id')
            ->selectRaw('s.fee, COUNT(DISTINCT su.user_id) as users')
            ->where('su.status', 'active')
            ->groupBy('s.fee')
            ->orderBy('users', 'desc')
            ->get()
            ->map(fn($r) => ['fee' => (string)$r->fee, 'users' => (int)$r->users]);

        $data = $raw->map(function(array $row) use ($totalActiveUsers){
            $percentage = $totalActiveUsers ? round(($row['users'] / $totalActiveUsers) * 100, 2) : 0.0;
            return [
                'fee' => $row['fee'],
                'users' => $row['users'],
                'percentage' => $percentage,
                'fee_translated' => $this->translateFee($row['fee']),
            ];
        })->values()->toArray();

        return [
            'data' => $data,
            'total_active_users' => $totalActiveUsers,
            'average_age' => $this->averageAgeActiveUsers(),
        ];
    }

    private function countDistinctActiveUsers(): int
    {
        return (int) DB::table('subscription_user as su')
            ->where('su.status', 'active')
            ->distinct('su.user_id')
            ->count('su.user_id');
    }

    private function translateFee(string $fee): string
    {
        return match ($fee) {
            'monthly' => 'Mensual',
            'quarterly' => 'Trimestral',
            'yearly' => 'Anual',
            default => ucfirst($fee),
        };
    }

    private function averageAgeActiveUsers(): ?float
    {
        $users = User::query()
            ->whereNotNull('birth_date')
            ->whereHas('subscriptions', function($q){
                $q->where('status','active');
            })
            ->get(['birth_date']);

        if ($users->isEmpty()) {
            return null;
        }

        $ages = $users->map(fn($u) => Carbon::parse($u->birth_date)->age);
        return round($ages->avg(), 2);
    }
}
