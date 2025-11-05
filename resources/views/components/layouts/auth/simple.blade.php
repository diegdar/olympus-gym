<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <!-- Navbar -->
    <x-navbar>
    </x-navbar>        
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 pb-6 md:p-3">
            {{-- logo --}}
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                        <!-- logo: light-mode -->
                        <img src="{{ asset('img/logos/gym-logo-for-light-mode.webp') }}"
                            alt="Logo del gimnasio" 
                            class="block dark:hidden w-[120px] h-[100px] object-contain" />
                        <!-- logo: dark-mode -->
                        <img src="{{ asset('img/logos/gym-logo-for-dark-mode.webp') }}"
                            alt="Logo del gimnasio" 
                            class="hidden dark:block w-[120px] h-[100px] object-contain" />                         
                        <p class="text-sm fond-bold">Olympus Gym</p>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
    @vite('resources/js/navbar.js')
</html>
