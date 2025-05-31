<!-- resources/views/schedule-table.blade.php -->
<x-layouts.app>
    <div class="relative transform-none">
        <x-slot name="title">Horario Actividades</x-slot>

        {{-- Mensaje de alerta --}}
        @if (session('msg'))
            <div id="message"
                class="absolute left-0 top-5 w-full z-50 bg-green-100 
                    border border-green-400 text-green-700 px-4 py-3 rounded 
                    dark:bg-green-800 dark:border-green-700 dark:text-green-200 text-center"
                role="alert">
                <span class="block sm:inline font-bold">{{ session('msg') }}</span>
            </div>
        @endif
        <h1 class="mb-3 text-2xl font-bold mt-2">Horario Actividades</h1>
        <div class="overflow-auto mb-4 sm:overflow-y-auto sm:max-h-[500px]">
            <table class="table-auto w-full">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-900">
                        <th
                            class="sticky top-0 left-0 z-20 bg-gray-100 
                                dark:bg-gray-900 px-5 py-3 border-b-2 border-gray-200 
                                dark:border-gray-700 text-lg font-bold text-center 
                                dark:text-red-300 hidden md:table-cell">
                            Hora
                        </th>
                        @foreach ($schedules as $day => $slots)
                            <th
                                class="sticky top-0 bg-gray-100 dark:bg-gray-900
                                     z-10 px-5 py-3 border-b-2 border-gray-200 
                                     dark:border-gray-700 text-g font-bold uppercase 
                                     text-center dark:text-red-300">
                                {{ ucfirst($day) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allTimes as $time)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-100 hover:border-2">
                            <td
                                class="sticky left-0 px-5 py-5 border-b 
                                    border-gray-200 dark:border-gray-700 
                                    text-xl text-center font-medium bg-white
                                    dark:bg-gray-900 hidden md:table-cell">
                                {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('H:i') }}
                            </td>
                            @foreach ($schedules as $day => $slots)
                                <td
                                    class="px-5 py-5 border-b 
                                        border-gray-200 
                                        dark:border-gray-700 align-top bg-white 
                                        dark:bg-gray-900">
                                    <span class="md:hidden font-bold dark:text-red-300">
                                        {{ ucfirst($day) }}:
                                    </span>
                                    <div class="flex justify-center align-content gap-2">
                                        @if (isset($slots[$time]))
                                            {{-- Mostrar las actividades --}}
                                            @foreach ($slots[$time] as $entry)
                                                <div
                                                    class="w-fit m-2 md:m-0 sm:mb-2 p-2 bg-blue-50 
                                                dark:bg-gray-700 rounded">
                                                    <article
                                                        class="font-semibold text-sm 
                                                    dark:text-gray-200 text-center">
                                                        {{ $entry['activity_name'] }}
                                                    </article>
                                                    <article
                                                        class="font-semibold text-sm 
                                                    dark:text-yellow-200 text-center">
                                                        {{ $entry['room_name'] }}
                                                    </article>
                                                    @can('activities.schedule.show')
                                                        <article
                                                            class="font-semibold text-sm 
                                                    dark:text-blue-200 text-center">
                                                            <a href="{{ route('activities.schedule.show', $entry['activity_schedule_id']) }}"
                                                                class="text-green-500 hover:text-green-700">
                                                                Ver Actividad
                                                        </article>
                                                    @endcan
                                                </div>
                                            @endforeach
                                        @else
                                            <div
                                                class="text-gray-400 dark:text-gray-600 
                                            text-center text-sm">
                                                —
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Opciones adicionales o scripts --}}
        @vite('resources/css/tableList.css')
        @vite('resources/js/messageTransition.js')
    </div>
</x-layouts.app>
