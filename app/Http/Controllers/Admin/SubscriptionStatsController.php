<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubscriptionStatsController extends Controller
{
    public function index(): View
    {
        return view('admin.subscriptions.stats');
    }

    public function percentages(): JsonResponse
    {
        // Total active users with at least one active subscription
        $totalActiveUsers = DB::table('subscription_user as su')
            ->where('su.status', 'active')
            ->distinct('su.user_id')
            ->count('su.user_id');

        $rows = DB::table('subscription_user as su')
            ->join('subscriptions as s', 's.id', '=', 'su.subscription_id')
            ->select('s.fee', DB::raw('COUNT(DISTINCT su.user_id) as users'))
            ->where('su.status', 'active')
            ->groupBy('s.fee')
            ->get()
            ->map(function ($row) use ($totalActiveUsers) {
                $row->percentage = $totalActiveUsers ? round(($row->users / $totalActiveUsers) * 100, 2) : 0;
                $row->fee_translated = match ($row->fee) {
                    'monthly' => 'Mensual',
                    'quarterly' => 'Trimestral',
                    'yearly' => 'Anual',
                    default => $row->fee,
                };
                return $row;
            })
            ->values();

        return response()->json([
            'data' => $rows,
            'total_active_users' => $totalActiveUsers,
        ]);
    }
}
