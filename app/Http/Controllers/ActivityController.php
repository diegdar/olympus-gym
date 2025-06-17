<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActivityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:activities.index', only: ['index']),
            new Middleware('permission:activities.show', only: ['show']),
            new Middleware('permission:activities.create', only: ['create', 'store']),
            new Middleware('permission:activities.edit', only: ['edit', 'update']),
            new Middleware('permission:activities.destroy', only: ['destroy']),
        ];
    }

    /**
     * Displays a list of registered activities.
     *
     * Redirects to the activity creation page if no activities are registered.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(): RedirectResponse|View
    {
        $activities = Activity::all();
        if ($activities->isEmpty()) {
            return redirect()->route('activities.create')->with('msg', 'No hay salas creadas aun, por favor crea una.');
        }

        return view('activities.index', compact('activities'));
    }

    /**
     * Display the specified activity.
     *
     * @param Activity $activity The activity to be displayed.
     * @return \Illuminate\View\View The view displaying the activity.
     */
    public function show(Activity $activity): View
    {
        return view('activities.show', compact('activity'));
    }

    /**
     * Shows the form to create a new activity.
     *
     * @return \Illuminate\View\View The view with the form to create a new activity.
     */
    public function create(): View
    {
        return view('activities.create');
    }

    /**
     * Validates and stores a new activity in the database.
     *
     * The request must contain the name, description, and duration of the activity.
     * The name must be between 3 and 50 characters, and must be unique.
     * The description can be null, and must be between 10 and 2000 characters if it is not null.
     * The duration must be an integer greater than or equal to 15.
     *
     * If the request is valid, the activity will be stored in the database.
     * The user will then be redirected to the activities list with a success message.
     *
     * @param Request $request The request containing the activity's information.
     * @return RedirectResponse The redirect response containing the success message.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:50', 'unique:activities,name'],
            'description' => ['nullable', 'min:10', 'string', 'max:2000'],
            'duration' => ['required', 'integer', 'min:15'],
        ]);

        Activity::create($request->all());

        return redirect()->route('activities.index')->with('msg', 'Actividad creada correctamente.');
    }

    /**
     * Shows the form to edit an existing activity.
     *
     * @param Activity $activity The activity to be edited.
     * @return \Illuminate\View\View The view with the form to edit the activity.
     */
    public function edit(Activity $activity): View
    {
        return view('activities.edit', compact('activity'));
    }

    /**
     * Updates an existing activity in the database.
     *
     * The request must contain the name, description, and duration of the activity.
     * The name must be between 3 and 50 characters.
     * The description can be null, and must be between 10 and 2000 characters if it is not null.
     * The duration must be an integer greater than or equal to 15.
     *
     * If the request is valid, the activity will be updated in the database.
     * The user will then be redirected to the activities list with a success message.
     *
     * @param Request $request The request containing the activity's updated information.
     * @param Activity $activity The activity to be updated.
     * @return RedirectResponse The redirect response containing the success message.
     */
    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'description' => ['nullable', 'min:10', 'string', 'max:2000'],
            'duration' => ['required', 'integer', 'min:15'],
        ]);

        $activity->update($request->all());

        return redirect()->route('activities.index')->with('msg', 'Actividad actualizada correctamente.');
    }

    /**
     * Removes an existing activity from the database.
     *
     * @param Activity $activity The activity to be deleted.
     * @return RedirectResponse The redirect response containing the success message.
     */
    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->route('activities.index')->with('msg', 'Actividad eliminada correctamente.');
    }
}