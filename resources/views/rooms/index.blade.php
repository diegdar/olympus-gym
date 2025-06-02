<x-layouts.app>
    <div class="relative transform-none">
        <x-slot name="title">Listar salas</x-slot>
        <h1 class="mb-3">Lista de salas</h1>

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
                                </span> {{ $room->description }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @vite('resources/css/tableList.css')
        @vite('resources/js/hideTableHeaders.js')
    </div>
</x-layouts.app>
