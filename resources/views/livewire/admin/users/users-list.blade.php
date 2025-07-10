<div wire:poll.5s class="dark:bg-gray-800 dark:text-gray-100">
    <!-- Users list -->
    <div class="w-full px-4">
        <hr class="border-t border-gray-300 my-2 dark:border-gray-700">
        <h2 class= "text-center sm:text-start text-2xl dark:text-gray-100">
            Usuarios totales ({{ $usersCount }})
        </h2>
    </div>
    {{-- filter and search --}}
    <div class="flex flex-col ml-2">
        {{-- rows filter --}}
        <select wire:model.live='numberRows'
            class="mt-3 shadow max-w-[60px] appearance-none border rounded py-2 
                px-3 text-gray-700 leading-tight focus:outline-none 
                focus:shadow-outline dark:bg-gray-700 
                dark:border-gray-600 dark:text-gray-300"
            id="numberRows" name="numberRows">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="20">20</option>
        </select>
        {{-- box search --}}
        <input wire:model.live="search" type="search"
            class="shadow max-w-[250px] appearance-none border rounded 
                w-full py-2 px-3 text-gray-700 leading-tight 
                focus:outline-none focus:shadow-outline mt-2
                dark:bg-gray-700 dark:border-gray-600 
                dark:text-gray-300"
            placeholder="Buscar..."
            id="search" name="search">
    </div>
    <!-- Table: users list -->
    @if ($users->count())
        {{-- Pagination --}}
        <div class="mt-4 mb-1 mr-2 ml-2">
            {{ $users->links() }}
        </div>
        <!-- Table -->
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-lg font-bold uppercase dark:bg-gray-900 dark:border-gray-700 dark:text-red-300">
                        Nº
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-lg font-bold uppercase dark:bg-gray-900 dark:border-gray-700 dark:text-red-300">
                        Nombre
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-lg font-bold uppercase dark:bg-gray-900 dark:border-gray-700 dark:text-red-300">
                        Email
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-lg font-bold uppercase dark:bg-gray-900 dark:border-gray-700 dark:text-red-300">
                        Role
                    </th>
                    @can(['admin.users.destroy', 'admin.users.edit'])
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-lg font-bold uppercase dark:bg-gray-900 dark:border-gray-700 dark:text-red-300">
                            Acción
                        </th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                {{-- users list --}}
                @foreach ($users as $user)
                    <tr class="dark:bg-gray-800 dark:border-gray-700 block md:table-row dark:hover:bg-gray-100">
                        <td
                            class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell">
                            <span class="md:hidden font-bold dark:text-red-300">
                                Nº:
                            </span> {{ $user->id }}
                        </td>
                        <td
                            class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell">
                            <span class="md:hidden font-bold dark:text-red-300">
                                Nombre:
                            </span> {{ $user->name }}
                        </td>
                        <td
                            class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell">
                            <span class="md:hidden font-bold dark:text-red-300">
                                Email:
                            </span> {{ $user->email }}
                        </td>
                        <td
                            class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell">
                            <span class="md:hidden font-bold dark:text-red-300">
                                Role:
                            </span> {{ $user->getRoleNames()->implode(', ') }}
                        </td>
                        {{-- Action buttons --}}
                        @can(['admin.users.destroy', 'admin.users.edit'])
                            <td
                                class="py-0 sm:py-3 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300  md:table-cell">
                                <span class="md:hidden font-bold dark:text-red-300">
                                    Acción:
                                </span>
                                <div class="flex flex-row sm:flex-col justify-center items-center gap-4 sm:gap-0">
                                    <a class="block w-[50px] text-white-600 
                                    bg-blue-500 dark:hover:bg-blue-700 px-2 py-1 rounded dark:bg-blue-600 cursor-pointer"
                                        href="{{ route('admin.users.edit', $user->id) }}">
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                        class="inline-block ">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="block w-[50px] mt-0 sm:mt-2 text-white-600 bg-red-500 hover:bg-red-600 px-2 py-1 rounded cursor-pointer"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar el usuario {{ $user->name }}?')">
                                            Borrar
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class= "font-bold text-gray-500 dark:text-gray-400">No hay registros</p>
    @endif

    @vite('resources/css/tableList.css')
    @vite('resources/js/hideTableHeaders.js')

</div>