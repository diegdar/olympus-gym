<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\ActivityScheduleListService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ActivityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:activities.index', only: ['index']),
        ];
    }

    public function index(ActivityScheduleListService $scheduleService)
    {
        [$schedules, $allTimes] = $scheduleService();

        return view('activities.index', compact('schedules', 'allTimes'));
    }

    public function show(Activity $activity)
    {
        return view('activities.show', compact('activity'));
    }

}
