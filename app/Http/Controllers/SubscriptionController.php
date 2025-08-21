<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $subscription = $user
                         ->subscriptions()
                          ->wherePivot('status', 'active')->first();

        return view('users.subscription', compact('subscription', 'user'));
    }
}
