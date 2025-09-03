<?php
declare(strict_types=1);

namespace App\Services\Subscriptions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class SubscriptionMonthlyNetAggregator
{
    public function __invoke(int $year): array
    {
        $expr = $this->monthExpressions();
        $signups = $this->signupsQuery($expr['start'], $year)->pluck('signups','month');
        $cancellations = $this->cancellationsQuery($expr['end'], $year)->pluck('cancellations','month');
        $data = $this->allMonths()->map(function(string $m) use ($signups, $cancellations) {
            $up = (int) ($signups[$m] ?? 0);
            $down = (int) ($cancellations[$m] ?? 0);
            return [
                'month' => $m,
                'month_name' => self::MONTH_NAMES[$m],
                'signups' => $up,
                'cancellations' => $down,
                'net' => $up - $down,
            ];
        })->toArray();
        return ['year' => $year, 'data' => $data];
    }

    private function monthExpressions(): array
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            return [ 'start' => 'strftime("%m", start_date)', 'end' => 'strftime("%m", end_date)' ];
        }
        return [ 'start' => 'LPAD(MONTH(start_date),2,"0")', 'end' => 'LPAD(MONTH(end_date),2,"0")' ];
    }

    private function signupsQuery(string $monthExpr, int $year)
    {
        return DB::table('subscription_user')
            ->selectRaw($monthExpr.' as month, COUNT(DISTINCT id) as signups')
            ->whereYear('start_date', $year)
            ->groupBy('month');
    }

    private function cancellationsQuery(string $monthExpr, int $year)
    {
        return DB::table('subscription_user')
            ->selectRaw($monthExpr.' as month, COUNT(DISTINCT id) as cancellations')
            ->whereNotNull('end_date')
            ->whereYear('end_date', $year)
            ->groupBy('month');
    }

    private function allMonths(): Collection
    {
        return collect(range(1,12))->map(fn($m) => str_pad((string)$m,2,'0',STR_PAD_LEFT));
    }

    private const MONTH_NAMES = [
        '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
        '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
        '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
    ];
}
