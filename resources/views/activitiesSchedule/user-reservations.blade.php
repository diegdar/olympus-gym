<x-layouts.app>
    <div class="relative transform-none">
        <x-slot name="title">Horario Actividades</x-slot>
        {{-- alert message --}}
        @if (session('success'))
            <div id="message" class="absolute left-0 top-5 w-full z-50 bg-green-100 
                        border border-green-400 text-green-700 px-4 py-3 rounded 
                        dark:bg-green-800 dark:border-green-700 dark:text-green-200 text-center" role="alert">
                <span class="block sm:inline font-bold">{{ session('success') }}</span>
            </div>
        @elseif (session('error'))
            <div id="message" class="absolute left-0 top-5 w-full z-50 bg-red-100 
                        border border-red-400 text-red-700 px-4 py-3 rounded 
                        dark:bg-red-800 dark:border-red-700 dark:text-red-200 text-center" role="alert">
                <span class="block sm:inline font-bold">{{ session('error') }}</span>
            </div>
        @endif
        <h1 class="mb-3 text-2xl font-bold mt-2 mx-2">Mis Reservas</h1>
        <div class="overflow-auto mb-4 sm:overflow-y-auto sm:max-h-[500px]">
            <table class="table-auto w-full">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-900">
                        <th class="sticky top-0 bg-gray-100 dark:bg-gray-900
                                    z-10 px-5 py-3 border-b-2 border-gray-200 
                                    dark:border-gray-700 text-g font-bold 
                                    text-center dark:text-red-300">
                            Fecha/Hora:
                        </th>
                        <th class="sticky top-0 left-0 z-20 bg-gray-100 
                                dark:bg-gray-900 px-5 py-3 border-b-2 border-gray-200 
                                dark:border-gray-700 text-lg font-bold text-center 
                                dark:text-red-300 hidden md:table-cell">
                            Actividad:
                        </th>
                        <th class="sticky top-0 bg-gray-100 dark:bg-gray-900
                                    z-10 px-5 py-3 border-b-2 border-gray-200 
                                    dark:border-gray-700 text-g font-bold uppercase 
                                    text-center dark:text-red-300">
                            Sala:
                        </th>
                        <th class="sticky top-0 bg-gray-100 dark:bg-gray-900
                                    z-10 px-5 py-3 border-b-2 border-gray-200 
                                    dark:border-gray-700 text-g font-bold uppercase 
                                    text-center dark:text-red-300">
                            Acciones:
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservations as $reservation)
                        <tr class="dark:bg-gray-800 dark:border-gray-700 block md:table-row dark:hover:bg-gray-100">
                            <td
                                class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell text-left sm:text-center">
                                <span class="md:hidden font-bold dark:text-red-300">Fecha/Hora:</span>
                                {{ $reservation->start_datetime }}
                            </td>
                            <td
                                class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell text-left sm:text-center">
                                <span class="md:hidden font-bold dark:text-red-300">Actividad:</span>
                                {{ $reservation->activity->name }}
                            </td>
                            <td
                                class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell text-left sm:text-center">
                                <span class="md:hidden font-bold dark:text-red-300">Sala:</span>
                                {{ $reservation->room->name }}
                            </td>
                            <td
                                class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell text-left sm:text-center">
                                <div class="flex justify-center my-1 gap-1">
                                    {{-- show button --}}
                                    <article
                                        class="text-center block w-[50px] text-white-600   
                                        bg-yellow-500 dark:hover:bg-yellow-700 px-2 py-1 rounded dark:bg-yellow-600 cursor-pointer">
                                        <a href="{{ route('activity.schedules.show', $reservation->id) }}"
                                            class="text-white-600">
                                            Ver
                                    </article>
                                    {{-- unenroll button --}}
                                    <form method="POST"
                                        action="{{ route('activity.schedules.unenroll', $reservation->id) }}"
                                        class="flex justify-center ">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="block w-fit text-white-600 bg-red-500 hover:bg-red-600 px-2 py-1 rounded cursor-pointer"
                                            onclick="return confirm('¿Estás seguro de que deseas desinscribirte de la actividad {{ $reservation->activity->name }} del {{ $reservation->start_datetime }} ?')">
                                            Desinscribirse
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @vite('resources/css/tableList.css')
        @vite('resources/js/messageTransition.js')
    </div>
</x-layouts.app>