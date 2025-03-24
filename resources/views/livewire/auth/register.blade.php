<div class="flex flex-col gap-6">
    {{-- Font Awesome: icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <x-auth-header title="Crear cuenta" description="Introduce tus datos para crear tu cuenta" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            id="name"
            :label="__('Nombre')"
            type="text"
            name="name"
            required
            autofocus
            autocomplete="name"
            placeholder="Nombre y apellidos" />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            id="email"
            :label="__('Email address')"
            type="email"
            name="email"
            required
            autocomplete="email"
            placeholder="email@example.com" />
        <!-- Password -->
        <flux:input
            wire:model="password"
            id="password"
            :label="__('Contraseña')"
            type="password"
            name="password"
            required
            autocomplete="new-password"
            placeholder="Contraseña" />
        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            id="password_confirmation"
            :label="__('Confimar contraseña')"
            type="password"
            name="password_confirmation"
            required
            autocomplete="new-password"
            placeholder="Confimar contraseña" />
        <!-- privacy policy -->
         <div class="flex items-center">
            <flux:checkbox 
                wire:model="privacy"
                />
            <a href="{{ route('privacy.policy') }}"  target="_blank" class="ml-2">acepto la <span class="text-sm text-zinc-600 dark:text-zinc-400 underline hover:text-zinc-500">politica de privacidad</span></a>
         </div>
         @error('privacy') <span class="error text-red-500">{{ $message }}</span> @enderror
        <!-- create account button -->
        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Crear cuenta') }}
            </flux:button>
        </div>
    </form>
    <!-- Github register -->
    <div class="flex items-center justify-center">
        <a href="{{ route('auth.github') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:hover:bg-gray-600">
            {{ __('Registrarse mediante GitHub') }}
            <i class="fa-brands fa-github fa-lg ml-3" style="color: #eef6ff;"></i>
        </a>
    </div>
    <!-- Google register -->
    <div class="flex items-center justify-center">
        <a href="{{ route('auth.google') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:hover:bg-gray-600">
            {{ __('Registrarse mediante Google') }}
            <i class="fa-brands fa-google fa-lg ml-3" style="color: red;"></i>
        </a>
    </div>

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        Ya tienes una cuenta?
        <flux:link :href="route('login')" wire:navigate> Iniciar de sesion</flux:link>
    </div>

</div>