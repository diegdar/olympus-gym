<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use App\Models\User;
use \Illuminate\Http\RedirectResponse;
use \Illuminate\View\View;
use Illuminate\Http\Request;

class TwoFactorChallengeController extends Controller
{
    /**
     * The two factor authentication provider.
     */
    protected TwoFactorAuthenticationProvider $provider;

    private const INVALID_CODE = 'El código de autenticación de dos factores proporcionado no es válido.';
    private const INVALID_RECOVERY = 'El código de recuperación proporcionado no es válido.';
    private const NO_CODE_PROVIDED = 'Por favor, proporcione un código de autenticación o un código de recuperación.';
    private const INVALID_TOTP = 'El código de autenticación proporcionado no es válido.';

    /**
     * Create a new controller instance.
     */
    public function __construct(TwoFactorAuthenticationProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Show the two factor authentication challenge view.
     *
     * @param  Request  $request
     * @return RedirectResponse|View
     */
    public function create(Request $request): RedirectResponse|View
    {
        if (!$request->session()
             ->has('login.id')
        ) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }


    /**
     * Authenticates the user using a TOTP code or a recovery code.
     *
     * This function validates the request for a TOTP code or a recovery code.
     * If the TOTP code is provided, it authenticates the user using the TOTP
     * code. If the recovery code is provided, it authenticates the user using
     * the recovery code.
     *
     * @throws ValidationException If the TOTP code or the recovery code is invalid.
     */
    public function store(Request $request, ConfirmTwoFactorAuthentication $confirm): RedirectResponse
    {
        $request->validate([
            'code' => 'nullable|string|size:6',
            'recovery_code' => 'nullable|string',
        ]);

        $user = $this->getUserFromSession($request);

        $code = $request->input('code');
        $recoveryCode = $request->input('recovery_code');

        if (empty($code) && empty($recoveryCode)) {
            $this->throwValidationException('code', self::NO_CODE_PROVIDED);
        }

        if ($recoveryCode) {
            return $this->authenticateWithRecoveryCode($user, $recoveryCode, $request);
        }

        if ($code) {
            return $this->authenticateWithTotpCode($user, $code, $request, $confirm);
        }

        $this->throwValidationException('code', self::INVALID_CODE);
    }

    /**
     * Retrieves the user from the session.
     *
     * This function retrieves the user from the session using the login.id
     * key. If the user is not found, it throws a ValidationException
     * with a message indicating that the two factor authentication code
     * was invalid.
     *
     * @param Request $request The request to use.
     *
     * @return User The user retrieved from the session.
     *
     * @throws ValidationException If the user is not found or the two
     * factor authentication code is invalid.
     */
    private function getUserFromSession(Request $request): User
    {
        $userId = $request->session()->get('login.id');

        if (!$userId) {
            $this->throwValidationException('code', self::INVALID_CODE);
        }

        $user = User::find($userId);

        if (!$user || !$user->two_factor_secret) {
            $this->throwValidationException('code', self::INVALID_CODE);
        }

        return $user;
    }

    /**
     * Authenticates the user using a recovery code.
     *
     * @param User $user The user to authenticate.
     * @param string $recoveryCode The recovery code to use.
     * @param Request $request The request to use.
     *
     * @return RedirectResponse The redirect response to send after authentication.
     *
     * @throws ValidationException If the recovery code is invalid.
     */
    private function authenticateWithRecoveryCode(User $user, string $recoveryCode, Request $request): RedirectResponse
    {
        try {
            $recoveryCodes = json_decode(decrypt((string)$user->two_factor_recovery_codes), true);

            if (is_array($recoveryCodes)
                && in_array($recoveryCode, $recoveryCodes)
            ) {
                // Remove the used recovery code
                $remainingCodes = array_diff($recoveryCodes, [$recoveryCode]);
                $user->forceFill([
                    'two_factor_recovery_codes' => encrypt(json_encode(array_values($remainingCodes))),
                ])->save();

                return $this->loginAndRedirect($user, $request);
            }

        } catch (\Exception $e) {// Silently catches decryption/JSON errors; throws validation error for all invalid attempts

        }

        $this->throwValidationException('recovery_code', self::INVALID_RECOVERY);
    }

    /**
     * Authenticates the user using a TOTP code.
     *
     * @param User $user The user to authenticate.
     * @param string $code The TOTP code to use.
     * @param Request $request The request to use.
     * @param ConfirmTwoFactorAuthentication $confirm The confirmation action to perform after authentication.
     *
     * @return RedirectResponse The redirect response to send after authentication.
     *
     * @throws ValidationException If the TOTP code is invalid.
     */
    private function authenticateWithTotpCode(User $user, string $code, Request $request, ConfirmTwoFactorAuthentication $confirm): RedirectResponse
    {
        try {
            $secret = decrypt($user->two_factor_secret);
            if ($this->provider->verify($secret, $code)) {
                $confirm($user, $code);

                return $this->loginAndRedirect($user, $request);
            }
        } catch (\Exception $e) {
            $this->throwValidationException('code', self::INVALID_CODE);
        }

        $this->throwValidationException('code', self::INVALID_TOTP);
    }

    /**
     * Logs the user in and redirects them to their intended destination.
     *
     * Logs the user in using the provided request and user.
     * Regenerates the session and removes the 'login.id' key from the session.
     * Redirects the user to either the dashboard or the admin subscription statistics page, depending on their role.
     *
     * @param User $user The user to log in.
     * @param Request $request The request to use.
     *
     * @return RedirectResponse The redirect response to send after login.
     */
    private function loginAndRedirect(User $user, Request $request): RedirectResponse
    {
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->forget('login.id');

        if ($user?->hasRole('member')) {
            return redirect()->intended(route('dashboard'));
        }
        return redirect()->intended(route('admin.subscriptions.stats'));
    }

    /**
     * Throws a validation exception with the specified field and message.
     *
     * @param string $field The field that failed validation.
     * @param string $message The validation error message.
     *
     * @return never
     *
     * @throws ValidationException
     */
    private function throwValidationException(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
