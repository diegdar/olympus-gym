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
                wire:model="privacy" />
            <a href="{{ route('privacy.policy') }}" target="_blank" class="ml-2">acepto la <span class="text-sm text-zinc-600 dark:text-zinc-400 underline hover:text-zinc-500">politica de privacidad</span></a>
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

</div>