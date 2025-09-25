<?php
declare(strict_types=1);

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

        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            return redirect()->route('register', ['registerMessage'=>'No estas registrado con tu email que tienes en Google. Puedes hacerlo ahora!']);
        }    

        Auth::login($user);

        return redirect()->route('admin.subscriptions.stats');
    }

}
