<x-layouts.app>
    <div class="relative transform-none">
        <x-slot name="title">Listar salas</x-slot>
        <h1 class="mb-3">Lista de salas</h1>
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
        {{-- new room --}}
        @can(['rooms.create'])
            <div class="text-right mb-4 mt-3 mr-2">
                <a href="{{ route('rooms.create') }}"
                    class="bg-yellow-500 hover:bg-yellow-700 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-yellow-600 dark:hover:bg-yellow-700 cursor-pointer">
                    Crear sala
                </a>
            </div>
        @endcan
        {{-- rooms list --}}
        <div>
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-lg font-bold uppercase dark:bg-gray-900 dark:border-gray-700 dark:text-red-300 text-center">
                            Id
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-lg font-bold uppercase dark:bg-gray-900 dark:border-gray-700 dark:text-red-300">
                            Sala
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-lg font-bold uppercase dark:bg-gray-900 dark:border-gray-700 dark:text-red-300">
                            Descripción
                        </th>
                        @can(['rooms.edit'])
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-lg font-bold uppercase dark:bg-gray-900 dark:border-gray-700 dark:text-red-300 text-center">
                                Acción
                            </th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rooms as $room)
                        <tr class="dark:bg-gray-800 dark:border-gray-700 block md:table-row dark:hover:bg-gray-100">
                            <td
                                class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell text-left sm:text-center">
                                <span class="md:hidden font-bold dark:text-red-300">
                                    Id:
                                </span> {{ $room->id }}
                            </td>
                            <td
                                class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell text-left sm:text-center">
                                <span class="md:hidden font-bold dark:text-red-300">
                                    Sala:
                                </span> {{ $room->name }}
                            </td>
                            <td
                                class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell text-left sm:text-center">
                                <span class="md:hidden font-bold dark:text-red-300">
                                    Descripción:
                                </span>
                                <p class="pl-3">
                                    {{ $room->description }}
                                </p>
                            </td>
                            {{-- Action buttons --}}
                            @can(['rooms.edit'])
                                <td
                                    class="py-0 sm:py-3 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300  md:table-cell">
                                    <span class="md:hidden font-bold dark:text-red-300">
                                        Acción:
                                    </span>
                                    <div class="flex flex-row justify-center items-center gap-4">
                                        <a class="block w-[50px] text-white-600 
                                    bg-blue-500 dark:hover:bg-blue-700 px-2 py-1 rounded dark:bg-blue-600 cursor-pointer"
                                            href="{{ route('rooms.edit', $room->id) }}">
                                            Editar
                                        </a>
                                    </div>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @vite('resources/css/tableList.css')
        @vite('resources/js/hideTableHeaders.js')
        @vite('resources/js/messageTransition.js')
    </div>
</x-layouts.app>
