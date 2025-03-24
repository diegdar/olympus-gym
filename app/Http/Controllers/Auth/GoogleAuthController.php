<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
    ['email' => $googleUser->email],
        [
                    'name' => $googleUser->name ?? $googleUser->email,
                    'email' => $googleUser->email,
                ]
            );

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified(); // mark email as verified to avoid verify-email view
        }     

        Auth::login($user);

        return redirect()->route('dashboard');
    }

}
