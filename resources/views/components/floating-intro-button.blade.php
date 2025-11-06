<!-- Floating Intro Button -->
@if (!request()->routeIs('app-info'))
    <div class="fixed right-0 top-[96px] sm:top-[97px] transform -translate-y-1/2 z-50">
        <a href="{{ route('app-info') }}" target="_blank" class="block bg-2nd text-black font-bold py-2 px-2 rounded-l-lg transition-all duration-300 hover:px-4 focus:outline-none focus:ring-2 focus:ring-blue-300" style="clip-path: polygon(100% 0, 0 0, 0 70%, 20% 100%, 100% 100%);" title="Ir a introducción" aria-label="Abrir introducción en nueva pestaña">
            <span class="inline-block overflow-hidden whitespace-nowrap transition-all duration-300 w-9 hover:w-24">introducción</span>
        </a>
    </div>
@endif