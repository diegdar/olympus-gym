<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivitySchedules;
use App\Models\Activity;
use App\Models\Room;
use App\Services\ListActivityScheduleService;
use App\Services\ShowActivityScheduleService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActivityScheduleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:activities.schedule.index', only: ['index']),
            new Middleware('permission:activities.schedule.show', only: ['show']),
            new Middleware('permission:activities.schedule.create', only: ['create', 'store']),
        ];
    }
    
    /**
     * Displays a list of activity schedules.
     *
     * @param ListActivityScheduleService $scheduleService The service to list activity schedules.
     * @return \Illuminate\View\View The view displaying the list of activity schedules.
     */
    public function index(ListActivityScheduleService $scheduleService): View
    {
        [$schedules, $allTimes] = $scheduleService();

        return view('activitiesSchedule.index', compact('schedules', 'allTimes'));
    }

    /**
     * Displays an activity schedule.
     *
     * @param int $activityScheduleId The ID of the activity schedule to display.
     * @param ShowActivityScheduleService $showActivityScheduleService The service to show an activity schedule.
     * @return \Illuminate\View\View The view displaying the activity schedule.
     */
    public function show(int $activityScheduleId, ShowActivityScheduleService $showActivityScheduleService): View
    {
        $activitySchedule = $showActivityScheduleService($activityScheduleId);

        return view('activitiesSchedule.show', compact('activitySchedule'));
    }


    /**
     * Shows the form to create a new activity schedule.
     *
     * Retrieves all activities and rooms to populate the form.
     *
     * @return \Illuminate\View\View The view with the form to create a new activity schedule.
     */
    public function create(): View
    {
        $activities = Activity::all();;
        $rooms = Room::all();
        return view('activitiesSchedule.create', compact(['activities', 'rooms']));
    }

    /**
     * Validates and stores a new activity schedule in the database.
     *
     * The request must contain the activity ID, start and end datetime, room ID, and maximum and current enrollment.
     * The activity ID must exist in the activities table.
     * The start datetime must be after or equal to today.
     * The room ID must exist in the rooms table.
     * The end datetime must be after the start datetime.
     * The maximum enrollment must be between 10 and 50.
     * The current enrollment must be between 0 and the maximum enrollment.
     *
     * If the request is valid, the activity schedule will be stored in the database.
     * The user will then be redirected to the activities schedule list with a success message.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'start_datetime' => 'required|date|after_or_equal:today',
            'room_id' => 'required|exists:rooms,id',
            'end_datetime' => 'required|date|after:start_datetime',
            'max_enrollment' => 'required|integer|min:10|max:50',
            'current_enrollment' => 'required|integer|min:0|lte:max_enrollment',
        ]);

        ActivitySchedules::create($request->all());
        return redirect()->route('activities.schedule.index')->with('msg', 'Horario creado correctamente.');
    }

}
