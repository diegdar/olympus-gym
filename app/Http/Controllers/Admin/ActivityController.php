<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Schedule;
use App\Services\ActivityScheduleListService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ActivityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [            
            new Middleware('permission:admin.roles.index', only: ['index']),

        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ActivityScheduleListService $activityScheduleListService)
    {
        $records = $activityScheduleListService->getActivityScheduleRecords();
        $daysOfWeek = $records->pluck('day_of_week')->unique()->toArray();
        $timeSlots  = $records->pluck('start_time')->unique()->sort()->toArray();       
        $recordsMatrix = $activityScheduleListService
                            ->createScheduleMatrix($daysOfWeek, $timeSlots);
            
        return view('activities.index', compact('daysOfWeek', 'timeSlots', 'recordsMatrix'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
