<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function __invoke(Request $request)
    {
        $data = $this->service->buildViewData($request->user());
        return view('dashboard', $data);
    }

    public function weeklyAttendance(Request $request): JsonResponse
    {
        return response()->json($this->service->weeklyAttendanceStats($request->user()));
    }

    public function activityDistribution(Request $request): JsonResponse
    {
        return response()->json($this->service->activityDistribution($request->user()));
    }
}
