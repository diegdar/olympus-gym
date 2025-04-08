<div class="flex flex-col gap-6">
    {{-- Font Awesome: icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <x-auth-header title="Crear cuenta" description="Introduce tus datos para crear tu cuenta" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <!-- Form register -->
    @if ($registerMessage)
    <div class="bg-red-400 font-semibold text-center p-2 text-black">
        {{ $registerMessage }}
    </div>
    @endif

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
            :label="__('Email')"
            type="email"
            name="email"
            required
            autocomplete="email"
            placeholder="email@example.com" />

        <!-- chosen fee  -->
        <div>
            <label for="fee" class="block text-sm font-medium ">Cuota</label>
            <select wire:model="fee" id="fee" name="fee" class="mt-1 block w-full text-center py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:border-white-500 appearance-none">
                <option class="bg-bg_darkMode" value="">-Selecciona una cuota-</option>
                <option class="bg-bg_darkMode" value="monthly">Mensual</option>
                <option class="bg-bg_darkMode" value="quarterly">Trimestral</option>
                <option class="bg-bg_darkMode" value="yearly">Anual</option>
            </select>
            @error('fee') <span class="error text-red-500">{{ $message }}</span> @enderror
        </div>

        <!-- Password -->
        <div class="relative">
            <input
                wire:model="password"
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Contraseña"
                class="w-full pr-10 py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300" />
            <!-- eye-icon visibility-->
            <i id="toggle-password"
                class="fa-solid fa-eye absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-600 cursor-pointer"
                style="z-index: 2;">
            </i>
        </div>

        <!-- Password Confirmation -->
        <div class="relative mt-4">
            <input
                wire:model="password_confirmation"
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Confirmar contraseña"
                class="w-full pr-10 py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300" />
            <!-- eye-icon visibility-->
            <i id="toggle-password-confirmation"
                class="fa-solid fa-eye absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-600 cursor-pointer"
                style="z-index: 2;">
            </i>
        </div>
        
        <!-- privacy policy -->
        <div class="flex items-center">
            <flux:checkbox
                wire:model="privacy" />
            <section class="flex flex-wrap items-center ml-2">
                <span >Estoy de acuerdo con la</span>
                <a href="{{ route('privacy.policy') }}" target="_blank" class="ml-2 text-sm text-zinc-600 dark:text-zinc-400 underline hover:text-zinc-500">politica de privacidad</a>
            </section>
            <!-- <a href="{{ route('privacy.policy') }}" target="_blank" class="ml-2">acepto la <span class="text-sm text-zinc-600 dark:text-zinc-400 underline hover:text-zinc-500">politica de privacidad</span></a> -->
        </div>
        @error('privacy') <span class="error text-red-500">{{ $message }}</span> @enderror
        <!-- create account button -->
        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Crear cuenta') }}
            </flux:button>
        </div>
    </form>

    <!-- redirection to login -->
    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        Ya tienes una cuenta?
        <flux:link :href="route('login')" wire:navigate> Iniciar de sesion</flux:link>
    </div>

    @vite('resources/js/passwordVisibility.js')
</div>