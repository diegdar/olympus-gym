<?php
declare(strict_types=1);

namespace App\Providers;

use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use PragmaRX\Google2FA\Google2FA;

class Google2FAProvider implements TwoFactorAuthenticationProvider
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Generate a new secret key.
     *
     * @return string
     */
    public function generateSecretKey()
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Get the two factor authentication QR code URL.
     *
     * @param  string  $companyName
     * @param  string  $companyEmail
     * @param  string  $secret
     * @return string
     */
    public function qrCodeUrl($companyName, $companyEmail, $secret)
    {
        return $this->google2fa->getQRCodeUrl($companyName, $companyEmail, $secret);
    }

    /**
     * Verify the given token.
     *
     * @param  string  $secret
     * @param  string  $code
     * @return bool
     */
    public function verify($secret, $code)
    {
        return $this->google2fa->verifyKey($secret, $code);
    }
}
