<x-layouts.auth.simple>
    <div class="flex flex-col gap-6">
        @section('title', 'Two Factor Authentication')

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

    <div class="text-center text-sm text-gray-600 dark:text-gray-400">
        {{-- {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }} --}}
        {{ __('Por favor, confirma el acceso a tu cuenta ingresando el código de autenticación proporcionado por tu aplicación autenticadora o el código de recuperación de tu cuenta.') }}
    </div>

    <form method="POST" action="{{ route('two-factor.challenge') }}" class="flex flex-col gap-6">
        @csrf

        <!-- Authentication Code -->
        <flux:input
            id="code"
            :label="__('Código de Autenticación')"
            type="text"
            inputmode="numeric"
            name="code"
            autofocus
            autocomplete="one-time-code"
        />

        <!-- Recovery Code -->
        <flux:input
            id="recovery_code"
            :label="__('Código de Recuperación')"
            type="text"
            name="recovery_code"
            autocomplete="one-time-code"
        />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('Confirmar') }}
            </flux:button>
        </div>
    </form>
    </div>
</x-layouts.auth.simple>
