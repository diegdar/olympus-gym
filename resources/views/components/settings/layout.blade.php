<div class="flex items-start max-md:flex-col">
    <div class="mr-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('settings.profile')" :class="request()->routeIs('settings.profile') ? 'border-b-2 border-yellow-500' : ''" wire:navigate>{{ __('Perfil') }}</flux:navlist.item>
            <flux:navlist.item :href="route('settings.password')" :class="request()->routeIs('settings.password') ? 'border-b-2 border-yellow-500' : ''" wire:navigate>{{ __('Contraseña') }}</flux:navlist.item>
            <flux:navlist.item :href="route('settings.appearance')" :class="request()->routeIs('settings.appearance') ? 'border-b-2 border-yellow-500' : ''" wire:navigate>{{ __('Apariencia') }}</flux:navlist.item>
            <flux:navlist.item :href="route('settings.two-factor')" :class="request()->routeIs('settings.two-factor') ? 'border-b-2 border-yellow-500' : ''" wire:navigate>{{ __('Verificación en dos pasos') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
