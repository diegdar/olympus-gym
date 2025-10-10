<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuthentication extends Component
{
    /**
     * Indicates if two factor authentication QR code is being displayed.
     */
    public bool $showingQrCode = false;

    /**
     * Indicates if the two factor authentication confirmation input and button are being displayed.
     */
    public bool $showingConfirmation = false;

    /**
     * Indicates if the two factor authentication recovery codes are being displayed.
     */
    public bool $showingRecoveryCodes = false;

    /**
     * The OTP code for confirming two factor authentication.
     */
    public ?string $code;

    /**
     * The two factor authentication QR code SVG.
     */
    public string $qrCode = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        if ($this->enabled && is_null(Auth::user()->two_factor_confirmed_at)) {
            // If 2FA is enabled but not confirmed, show the confirmation step
            $this->showingQrCode = true;
            $this->showingConfirmation = true;
            $this->qrCode = Auth::user()->twoFactorQrCodeSvg();
        } elseif ($this->enabled) {
            // If 2FA is fully enabled, show recovery codes option
            $this->showingRecoveryCodes = false; // Don't show codes by default
        }
    }


    /**
     * Indicates if two factor authentication is enabled.
     */
    public function getEnabledProperty(): bool
    {
        return ! empty(Auth::user()->two_factor_secret);
    }

    /**
     * Enable two factor authentication for the user.
     */
    public function enableTwoFactorAuthentication(EnableTwoFactorAuthentication $enable): void
    {
        $enable(Auth::user());

        $this->showingQrCode = true;
        $this->showingConfirmation = true;

        $this->qrCode = Auth::user()->twoFactorQrCodeSvg();

        // Don't flash success here - wait for confirmation
    }

    /**
     * Confirm two factor authentication for the user.
     */
    public function confirmTwoFactorAuthentication(ConfirmTwoFactorAuthentication $confirm): void
    {
        $this->validate([
            'code' => 'required|string|size:6',
        ]);

        $confirm(Auth::user(), $this->code);

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = true;

        $this->code = null;

        session()->flash('status', 'two-factor-authentication-enabled');
    }

    /**
     * Disable two factor authentication for the user.
     */
    public function disableTwoFactorAuthentication(DisableTwoFactorAuthentication $disable): void
    {
        $disable(Auth::user());

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = false;

        session()->flash('status', 'two-factor-authentication-disabled');
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generate): void
    {
        $generate(Auth::user());

        $this->showingRecoveryCodes = true;
    }

    /**
     * Display the QR code for the user.
     */
    public function showQrCode(): void
    {
        $this->showingQrCode = true;
        $this->qrCode = Auth::user()->twoFactorQrCodeSvg();
    }

    /**
     * Display the recovery codes for the user.
     */
    public function showRecoveryCodes(): void
    {
        $this->showingRecoveryCodes = true;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.settings.two-factor-authentication');
    }
}
