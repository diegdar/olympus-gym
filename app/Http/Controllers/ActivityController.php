<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ActivityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:activities.index', only: ['index']),
            new Middleware('permission:activities.show', only: ['show']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = Activity::all();
        if ($activities->isEmpty()) {
            return redirect()->route('activities.create')->with('msg', 'No hay salas creadas aun, por favor crea una.');
        }

        return view('activities.index', compact('activities'));
    }   
    
    public function show(Activity $activity)
    {
        return view('activities.show', compact('activity'));
    }

}

