<x-layouts.app>
    <div class="relative transform-none">
        <x-slot name="title">Horario Actividades</x-slot>
        {{-- alert message --}}
        @if (session('msg'))
            <div id="message"
                class="absolute left-0 top-5 w-full z-50 bg-green-100 
                    border border-green-400 text-green-700 px-4 py-3 rounded 
                    dark:bg-green-800 dark:border-green-700 dark:text-green-200 text-center"
                role="alert">
                <span class="block sm:inline font-bold">{{ session('msg') }}</span>
            </div>
        @endif
        <h1 class="mb-3 text-2xl font-bold mt-2 mx-2">Horario Actividades</h1>
        {{-- new activity schedule --}}
        @can(['activity.schedules.create'])
            <div class="text-center sm:text-right mb-4 mt-7 mr-0 sm:mr-6 md:mr-3">
                <a href="{{ route('activity.schedules.create') }}"
                    class="bg-yellow-500 hover:bg-yellow-700 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-yellow-600 dark:hover:bg-yellow-700 cursor-pointer">
                    Crear horario actividad
                </a>
            </div>
        @endcan
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
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-100 hover:border-2 mx-1">
                            <td
                                class="sticky left-0 px-5 py-5 border-b 
                                    border-gray-200 dark:border-gray-700 
                                    text-xl text-center font-medium bg-white
                                    dark:bg-gray-900 hidden md:table-cell">
                                {{ $time }}
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
                                    <div class="flex flex-wrap justify-center align-content gap-2">
                                        @if (isset($slots[$time]))
                                            {{-- Mostrar las actividades --}}
                                            @foreach ($slots[$time] as $entry)
                                                <div
                                                    class="w-fit m-2 md:m-0 sm:mb-2 p-2 bg-blue-50 
                                                dark:bg-gray-700 rounded">
                                                    <article
                                                        class="font-semibold text-sm 
                                                    dark:text-gray-200 text-center">
                                                        Actividad: {{ $entry['activity_name'] }}
                                                    </article>
                                                    <article
                                                        class="font-semibold text-sm 
                                                    dark:text-yellow-200 text-center">
                                                        Sala: {{ $entry['room_name'] }}
                                                    </article>
                                                    {{-- Action buttons --}}
                                                    <div class="flex justify-center my-1 gap-1">
                                                        {{-- show button --}}
                                                        @can('activity.schedules.show')
                                                            <article
                                                                class="text-center block w-[50px] text-white-600 
                                                            bg-yellow-500 dark:hover:bg-yellow-700 px-2 py-1 rounded dark:bg-yellow-600 cursor-pointer">
                                                                <a href="{{ route('activity.schedules.show', $entry['activity_schedule_id']) }}"
                                                                    class="text-white-600">
                                                                    Ver
                                                            </article>
                                                        @endcan
                                                        {{-- edit button --}}
                                                        @can('activity.schedules.edit')
                                                            <article
                                                                class="block w-[55px] text-white-600 
                                                            bg-blue-500 dark:hover:bg-blue-700 px-2 py-1 rounded dark:bg-blue-600 cursor-pointer">
                                                                <a href="{{ route('activity.schedules.edit', $entry['activity_schedule_id']) }}"
                                                                    class="text-white-600">
                                                                    Editar
                                                            </article>
                                                        @endcan
                                                        @can('activity.schedules.destroy')
                                                            {{-- Delete button --}}
                                                            <form method="POST" action="{{ route('activity.schedules.destroy', $entry['activity_schedule_id']) }}"
                                                                class="inline-block ">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="block w-[55px] text-white-600 bg-red-500 hover:bg-red-600 px-2 py-1 rounded cursor-pointer"
                                                                    onclick="return confirm('¿Estás seguro de que deseas eliminar la actividad {{ $entry['activity_name'] }} del {{$day}} a las {{ $time }}?')">
                                                                    Borrar
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>
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
