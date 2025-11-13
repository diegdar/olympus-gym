<x-layouts.app.sidebar>
    <!-- Floating Intro Button -->
    <x-floating-intro-button />    
    
    <flux:main class="mt-14 lg:mt-2">
        {{ $slot }}
    </flux:main>

    <flux:footer class="!p-0">
        <x-footer></x-footer>
    </flux:footer>
    
</x-layouts.app.sidebar>
