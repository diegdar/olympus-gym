<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GitHubAuthController extends Controller
{
    public function redirectToGitHub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGitHubCallback()
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::updateOrCreate(
        ['email' => $githubUser->email],
            [
                        'name' => $githubUser->nickname,
                        'email' => $githubUser->email,
                    ]
        );

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified(); // mark email as verified to avoid verify-email view
        }        

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
