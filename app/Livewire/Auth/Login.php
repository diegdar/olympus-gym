<?php
declare(strict_types=1);

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     *
     * Validates the input, checks for rate limiting, attempts login,
     * handles two-factor authentication if required, and completes the login process.
     *
     * @return void
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (!$this->attemptLogin()) {
            RateLimiter::hit($this->throttleKey());//increment number of failed login attempts

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        if ($this->requiresTwoFactor()) {
            $this->initiateTwoFactorChallenge();
            return;
        }

        $this->completeLogin();
    }

    /**
     * Attempt to authenticate the user.
     */
    private function attemptLogin(): bool
    {
        return Auth::attempt(
                [
                    'email' => $this->email,
                    'password' => $this->password
                ],
                $this->remember);
    }

    /**
     * Check if the user requires two-factor authentication.
     */
    private function requiresTwoFactor(): bool
    {
        $user = Auth::user();
        return $user->two_factor_secret
            && $user->two_factor_confirmed_at;
    }


    /**
     * Initiate a two-factor authentication challenge.
     *
     * Stores the user ID in the session to be used later, due to the the user is logged out for security, regenerates the session,
     * and redirects the user to the two-factor login page.
     * @return void
     */
    private function initiateTwoFactorChallenge(): void
    {
        Session::put('login.id', Auth::user()->id);
        Auth::logout();
        Session::regenerate();
        $this->redirect(route('two-factor.login'),
                navigate: true);
    }

    /**
     * Completes the login process after the user has been authenticated.
     *
     * Generates a new session ID to protect against session fixation attacks,
     * and redirects the user to their intended destination.
     *
     * @return void
     */
    private function completeLogin(): void
    {
        Session::regenerate();
        $this->redirectIntended(default: $this->postLoginRedirect(),
                navigate: true);
    }

    /**
     * Returns the URL that the user should be redirected to after logging in.
     *
     * If the user has the "member" role, redirects to the dashboard.
     * Otherwise, redirects to the admin subscription statistics page.
     *
     * @return string
     */
    private function postLoginRedirect(): string
    {
        $user = Auth::user();
        if ($user?->hasRole('member')) {
            return route('dashboard', absolute: false);
        }
        return route('admin.subscriptions.stats',
                absolute: false);
    }


    /**
     * Checks if the user has exceeded the maximum number of login attempts
     * and throws a ValidationException if so.
     *
     * If the user has exceeded the maximum number of login attempts, a Lockout
     * event is triggered and a validation exception is thrown with a message
     * showing the user the number of seconds they should wait before trying again.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));//trigger the lockout event for logging against

        $seconds = RateLimiter::availableIn($this->throttleKey());//get the number of seconds until the user can try again

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);//throw a validation exception showing the user the number of seconds they should wait
    }

    /**
     * Returns a throttle key combining the email and IP to track the number of failed login attempts.
     *
     * The throttle key is used to identify the user and their IP address, and is used to
     * rate limit the number of login attempts from a given IP address.
     *
     * @return string
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());//get the throttle key combining the email and ip to track the number of failed login attempts
    }
}
