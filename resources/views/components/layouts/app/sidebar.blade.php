<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="min-h-screen flex flex-col bg-white dark:bg-zinc-800">    <flux:sidebar sticky stashable class="z-25 border-r border-zinc-200 bg-zinc-50
                                     dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        {{-- My web icon --}}
        <div class="">
            <a href="https://diegochacondev.es" target="_blank">
                <img src="{{ asset('img/logos/my-web-logo.webp') }}" class="w-[50px] h-[40px]"
                    alt="logo Diego Chacon que redirige a su sitio web" title="Ir a portfolio Diego Chacon" />
            </a>
        </div>

        {{-- Main Panel --}}
        @can('member.panel')
            <flux:navlist variant="outline">
                <flux:navlist.group heading="Panel Principal" class="grid">
                    <flux:navlist.item icon="presentation-chart-bar" :href="route('dashboard')"
                        :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('home')" :current="request()->routeIs('home')"
                        wire:navigate>{{ __('Inicio') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="building-office" :href="route('facilities')"
                        :current="request()->routeIs('facilities')" wire:navigate>{{ __('Instalaciones') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="list-bullet" :href="route('services')"
                        :current="request()->routeIs('services')" wire:navigate>{{ __('Servicios') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="phone" :href="route('contact')" :current="request()->routeIs('contact')"
                        wire:navigate>{{ __('Contacto') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>
        @endcan

        {{-- Admin Panel --}}
        @can('admin.panel')
            <flux:navlist variant="outline">
                <flux:navlist.group heading="Gestion Operativa" class="grid">
                    {{-- Subscription Stats --}}
                    @can('admin.subscriptions.stats')
                        <flux:navlist.item icon="chart-pie" :href="route('admin.subscriptions.stats')"
                            :current="request()->routeIs('admin.subscriptions.stats')">
                            {{ __('Estadísticas Suscripciones') }}
                        </flux:navlist.item>
                    @endcan
                    {{-- Users --}}
                    @can('admin.users.index')
                        <flux:navlist.item icon="users" :href="route('admin.users.index')"
                            :current="request()->routeIs('admin.users.index')" wire:navigate>
                            {{ __('Usuarios') }}
                        </flux:navlist.item>
                    @endcan
                    {{-- Roles --}}
                    @can('admin.roles.index')
                        <flux:navlist.item icon="identification" :href="route('admin.roles.index')"
                            :current="request()->routeIs('admin.roles.index')" wire:navigate>
                            {{ __('Roles') }}
                        </flux:navlist.item>
                    @endcan
                    {{-- Rooms --}}
                    @can('rooms.index')
                        <flux:navlist.item icon="building-office-2" :href="route('rooms.index')"
                            :current="request()->routeIs('rooms.index')" wire:navigate>
                            {{ __('Salas') }}
                        </flux:navlist.item>
                    @endcan
                    {{-- Activities --}}
                    @can('activities.index')
                        <flux:navlist.item icon="list-bullet" :href="route('activities.index')"
                            :current="request()->routeIs('activities.index')" wire:navigate>
                            {{ __('Actividades') }}
                        </flux:navlist.item>
                    @endcan
                    {{-- Activities Schedule --}}
                    <flux:navlist.item icon="calendar" :href="route('activity.schedules.index')"
                        :current="request()->routeIs('activity.schedules.index')" wire:navigate>
                        {{ __('Horario Actividades') }}
                    </flux:navlist.item>                    
                </flux:navlist.group>
            </flux:navlist>
        @endcan

        {{-- User's Panel --}}
        @can('member.panel')
            <flux:navlist variant="outline">
                <flux:navlist.group heading="Mis Gestiones" class="grid">
                    {{-- Activities Schedule --}}
                    <flux:navlist.item icon="calendar" :href="route('activity.schedules.index')"
                        :current="request()->routeIs('activity.schedules.index')" wire:navigate>
                        {{ __('Horario Actividades') }}
                    </flux:navlist.item>
                    {{-- Activities Schedule --}}
                    @can('user.reservations')
                        <flux:navlist.item icon="ticket" :href="route('user.reservations')"
                            :current="request()->routeIs('user.reservations')" wire:navigate>
                            {{ __('Mis Reservas') }}
                        </flux:navlist.item>
                    @endcan
                    {{-- Activities Schedule --}}
                    @can('member.subscription')
                        <flux:navlist.item icon="check-badge" :href="route('member.subscription')"
                            :current="request()->routeIs('member.subscription')" wire:navigate>
                            {{ __('Mi Suscripción') }}
                        </flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            </flux:navlist>
        @endcan

        <flux:spacer />

        {{-- User Profile --}}
        <flux:dropdown position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon-trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    {{-- Mobile Header --}}
    <flux:header class="fixed top-0 left-0 w-full z-50 bg-white dark:bg-gray-900 shadow lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>Settings</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts

    <x-footer />
</body>

</html>