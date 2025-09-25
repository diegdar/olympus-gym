<?php
declare(strict_types=1);

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

        $user = User::where('email', $githubUser->email)->first();

        if (!$user) {
            return redirect()->route('register', ['registerMessage'=>'No estas registrado con tu email que tienes en Github. Puedes hacerlo ahora!']);
        }            

        Auth::login($user);

        return redirect()->route('admin.subscriptions.stats');
    }
}
