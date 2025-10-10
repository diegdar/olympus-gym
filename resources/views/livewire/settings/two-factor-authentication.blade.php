<section class="w-full px-4 sm:px-6 lg:px-8">
    @section('title', 'Autenticación de Dos Factores')
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Autenticación de Dos Factores')" :subheading="__('Agregue seguridad adicional a su cuenta utilizando autenticación de dos factores.')">
        <div class="mb-23">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    <h3 class="text-lg sm:text-xl font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Termine de habilitar la autenticación de dos factores.') }}
                    </h3>
                @else
                    <h3 class="text-lg sm:text-xl font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Ha habilitado la autenticación de dos factores.') }}
                    </h3>
                @endif
            @else
                <h3 class="text-lg sm:text-xl font-medium text-gray-900 dark:text-gray-100">
                    {{ __('No ha habilitado la autenticación de dos factores.') }}
                </h3>
            @endif

            <div class="mt-3 max-w-sm sm:max-w-md md:max-w-xl text-sm sm:text-base text-gray-600 dark:text-gray-400">
                <p>
                    {{ __('Cuando la autenticación de dos factores está habilitada, se le pedirá un código seguro(token) y aleatorio durante la autenticación. Puede recuperar este token desde la aplicación Google Authenticator de su teléfono.') }}
                </p>
            </div>

            @if (session('status') === 'two-factor-authentication-enabled')
                <div class="mt-4 font-medium text-sm sm:text-base text-green-600 dark:text-green-400">
                    {{ __('La autenticación de dos factores ha sido habilitada.') }}
                </div>
            @endif

            @if (session('status') === 'two-factor-authentication-disabled')
                <div class="mt-4 font-medium text-sm sm:text-base text-green-600 dark:text-green-400">
                    {{ __('La autenticación de dos factores ha sido deshabilitada.') }}
                </div>
            @endif

            @if ($this->enabled)
                @if ($showingQrCode)
                    <div class="mt-4 max-w-sm sm:max-w-md md:max-w-xl text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        <p class="font-semibold">
                            @if ($showingConfirmation)
                                {{ __('Para terminar de habilitar la autenticación de dos factores, escanee el siguiente código QR usando la aplicación autenticadora de su teléfono o ingrese la clave de configuración y proporcione el código OTP generado.') }}
                            @else
                                {{ __('La autenticación de dos factores ahora está habilitada. Escanee el siguiente código QR usando la aplicación autenticadora de su teléfono o ingrese la clave de configuración.') }}
                            @endif
                        </p>
                    </div>

                    <div class="mt-4 flex justify-center">
                        <div class="inline-block bg-white p-2 rounded-lg" wire:ignore>
                            {!! $qrCode !!}
                        </div>
                    </div>

                    <div class="mt-4 max-w-sm sm:max-w-md md:max-w-xl text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        <p class="font-semibold break-all sm:break-normal">
                            {{ __('Clave de Configuración') }}: {{ decrypt(auth()->user()->two_factor_secret) }}
                        </p>
                    </div>

                    @if ($showingConfirmation)
                        <div class="mt-4">
                            <flux:input wire:model="code" wire:keydown.enter="confirmTwoFactorAuthentication" :label="__('Código')"
                                type="text" name="code" inputmode="numeric" autofocus autocomplete="one-time-code" class="w-full" />
                        </div>
                    @endif
                @endif

                @if ($showingRecoveryCodes)
                    <div class="mt-4 max-w-sm sm:max-w-md md:max-w-xl text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        <p class="font-semibold">
                            {{ __('Almacene estos códigos de recuperación en un administrador de contraseñas seguro. Pueden usarse para recuperar el acceso a su cuenta si pierde su dispositivo de autenticación de dos factores.') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 max-w-sm sm:max-w-md md:max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 dark:bg-gray-900 rounded-lg">
                        @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                            <div>{{ $code }}</div>
                        @endforeach
                    </div>
                @endif
            @endif

            <div class="mt-5 flex flex-col sm:flex-row sm:flex-wrap gap-3">
                @if (!$this->enabled)
                    <flux:button wire:click="enableTwoFactorAuthentication" variant="primary" class="w-full sm:w-auto">
                        {{ __('Habilitar') }}
                    </flux:button>
                @else
                    @if ($showingRecoveryCodes)
                        <flux:button wire:click="regenerateRecoveryCodes" variant="filled" class="w-full sm:w-auto">
                            {{ __('Regenerar Códigos de Recuperación') }}
                        </flux:button>
                    @elseif ($showingConfirmation)
                        <flux:button wire:click="confirmTwoFactorAuthentication" variant="primary" class="w-full sm:w-auto">
                            {{ __('Confirmar') }}
                        </flux:button>
                    @else
                        <flux:button wire:click="showRecoveryCodes" variant="filled" class="w-full sm:w-auto">
                            {{ __('Mostrar Códigos de Recuperación') }}
                        </flux:button>
                    @endif

                    @if ($showingConfirmation)
                        <flux:button wire:click="disableTwoFactorAuthentication" variant="filled" class="w-full sm:w-auto">
                            {{ __('Cancelar') }}
                        </flux:button>
                    @else
                        <flux:button wire:click="disableTwoFactorAuthentication" variant="danger" class="w-full sm:w-auto">
                            {{ __('Deshabilitar') }}
                        </flux:button>
                    @endif
                @endif
            </div>
        </div>
    </x-settings.layout>
</section>
