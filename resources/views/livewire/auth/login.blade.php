<div class="flex flex-col gap-6">
    @section('title', 'Iniciar sesión')
    {{-- Font Awesome: icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">    

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email')"
            type="email"
            name="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <div class="relative">
            <input
                wire:model="password"
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Contraseña"
                class="w-full pr-10 py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300"
            />
            <!-- eye-icon visibility-->
            <i id="toggle-password"
            class="fa-solid fa-eye absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-600 cursor-pointer"
            style="z-index: 2;">
            </i>

            @if (Route::has('password.request'))
                <flux:link class="absolute right-0 -bottom-6 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('¿Has olvidado tu contraseña?') }}
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Recuerdame')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Iniciar sesion') }}</flux:button>
        </div>
    </form>
    <!-- social register -->
    <p class="text-lg font-bold">Si ya estas registrado puedes iniciar sesion tambien con:</p>
    <!-- Github login -->
    <div class="flex items-center justify-center">
        <a href="{{ route('auth.github') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:hover:bg-gray-600">
            {{ __('Iniciar sesión con GitHub') }}
            <i class="fa-brands fa-github fa-lg ml-3" style="color: #eef6ff;"></i>
        </a>
    </div>
    <!-- Google login -->
    <div class="flex items-center justify-center">
        <a href="{{ route('auth.google') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:hover:bg-gray-600">
            {{ __('Iniciar sesión con Google') }}
            <i class="fa-brands fa-google fa-lg ml-3" style="color: red;"></i>
        </a>
    </div>

    @if (Route::has('register'))
      <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
          No tienes una cuenta?
          <flux:link :href="route('register')" wire:navigate> Registrarse</flux:link>
      </div>
    @endif
    @vite('resources/js/passwordVisibility.js')
</div>