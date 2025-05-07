<x-layouts.app>
    <div class="relative transform-none">
        <x-slot name="title">Tabla de horarios</x-slot>
        <h1 class="mb-3 text-2xl font-bold">Horario Semanal del Gimnasio</h1>

        {{-- Mensaje de alerta --}}
        @if (session('msg'))
            <div id="message"
                class="absolute left-0 top-5 w-full z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded dark:bg-green-800 dark:border-green-700 dark:text-green-200 text-center"
                role="alert">
                <span class="block sm:inline font-bold">
                    {{ session('msg') }}
                </span>
            </div>
        @endif

        <div class="overflow-x-auto mb-4">
            <table class="min-w-full leading-normal bg-white dark:bg-gray-800">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-900">
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-lg font-bold uppercase text-center dark:text-red-300">
                            Hora
                        </th>
                        @foreach ($daysOfWeek as $day)
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-lg font-bold uppercase text-center dark:text-red-300">
                                {{ ucfirst($day) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($timeSlots as $time)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td
                                class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 text-sm text-left sm:text-center font-medium bg-white dark:bg-gray-900">
                                <span class="md:hidden font-bold dark:text-red-300">Hora:</span>
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $time)->format('H:i') }}
                            </td>
                            @foreach ($daysOfWeek as $day)
                                @php
                                    $entries = $recordsMatrix[$day][$time] ?? collect();
                                @endphp
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 align-top bg-white dark:bg-gray-900">
                                    @forelse ($entries as $entry)
                                        <div class="mb-2 p-2 bg-blue-50 dark:bg-gray-700 rounded">
                                            <p class="font-semibold text-sm dark:text-gray-200">
                                                {{ $entry['activity_name'] }}</p>
                                            <p class="text-xs dark:text-gray-400">
                                                Lugar: 
                                                {{ $entry['room_name'] }}
                                            </p>
                                            <p class="text-xs dark:text-gray-400">
                                                Duracion:
                                                {{ $entry['duration'] . ' mins.' }}
                                            </p>
                                        </div>
                                    @empty
                                        <span class="text-gray-400 dark:text-gray-600">—</span>
                                    @endforelse
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Opciones adicionales o scripts --}}
        @vite('resources/css/tableList.css')
        @vite('resources/js/hideTableHeaders.js')
        @vite('resources/js/messageTransition.js')
    </div>
</x-layouts.app>
