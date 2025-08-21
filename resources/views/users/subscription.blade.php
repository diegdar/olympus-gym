<x-layouts.app>
    <x-slot name="title">Mi suscripción</x-slot>
    <h1 class="mb-3">Mi suscripción</h1>
    {{-- Mensaje de alerta --}}
    @if (session('msg'))
        <div id="message"
            class="absolute left-0 top-5 w-full z-50 inline-block bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded dark:bg-green-800 dark:border-green-700 dark:text-green-200 text-center"
            room="alert">
            <span class="block sm:inline font-bold">
                {{ session('msg') }}
            </span>
        </div>
    @endif

    {{-- Subscription details --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <p class="mt-2 flex flex-col sm:flex-row"><strong class="font-bold dark:text-red-300 mr-2">Usuario:</strong> {{ $user->name }}
        </p>
        <p class="mt-2 flex flex-col sm:flex-row"><strong class="font-bold dark:text-red-300 mr-2">Cuota:</strong>
            {{ $subscription->fee_translated ?? 'No asignada' }}
        </p>
        <p class="mt-2 flex flex-col sm:flex-row"><strong class="font-bold dark:text-red-300 mr-2">Fecha de inicio:</strong>
            {{ $subscription->pivot->start_date_formatted ?? 'No asignada' }}
        </p>
        <p class="mt-2 flex flex-col sm:flex-row"><strong class="font-bold dark:text-red-300 mr-2">Fecha de finalización:</strong>
            {{ $subscription->pivot->end_date_formatted ?? 'No asignada' }}
        </p>
    </div>
</x-layouts.app>