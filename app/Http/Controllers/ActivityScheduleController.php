<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ListActivityScheduleService;
use App\Services\ShowActivityScheduleService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ActivityScheduleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:activities.schedule.index', only: ['index']),
            new Middleware('permission:activities.schedule.show', only: ['show']),
        ];
    }

    public function index(ListActivityScheduleService $scheduleService)
    {
        [$schedules, $allTimes] = $scheduleService();

        return view('activitiesSchedule.index', compact('schedules', 'allTimes'));
    }

    public function show(int $activityScheduleId, ShowActivityScheduleService $showActivityScheduleService)
    {
        $activitySchedule = $showActivityScheduleService($activityScheduleId);

        return view('activitiesSchedule.show', compact('activitySchedule'));
    }

}
