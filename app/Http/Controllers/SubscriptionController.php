<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\StoreSubscriptionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SubscriptionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:member.subscription', only: ['index']),
            new Middleware('permission:member.change-subscription', only: ['changeSubscription']),
        ];
    }

    public function index()
    {
        $user = Auth::user();
        $currentSubscription = $user
                         ->subscriptions()
                          ->wherePivot('status', 'active')->first();
        $subscriptions = Subscription::all();

        return view('users.subscription', compact('currentSubscription', 'user', 'subscriptions'));
    }

    public function changeSubscription(Request $request, StoreSubscriptionService $storeSubscriptionService)
    {
        $user = Auth::user();
        $data = $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
        ], [
            'subscription_id.required' => 'Debes elegir una suscripcion',
            'subscription_id.exists' => 'La suscripción seleccionada no es válida',
        ]);

        $changed = $storeSubscriptionService($user, (int) $data['subscription_id']);

        if (! $changed) {
            return back()->withErrors(['subscription_id' => 'Ya estabas suscrito en esta cuota, elige otra.'])->withInput();
        }

    return redirect()->route('member.subscription')->with('msg', 'Suscripción cambiada correctamente.');
    }
}
