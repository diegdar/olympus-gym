<x-layouts.app>

    <x-slot name="title">Listar roles</x-slot>
    <h1>Listar roles</h1>
    {{-- create role --}}
    <div>
        {{-- TODO: crear componente livewire para crear roles --}}
    </div>
    {{-- roles list --}}
    <div>
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-lg font-bold uppercase dark:bg-gray-900 dark:border-gray-700 dark:text-red-300 text-center">
                        ID
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-lg font-bold uppercase dark:bg-gray-900 dark:border-gray-700 dark:text-red-300">
                        Role
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-lg font-bold uppercase dark:bg-gray-900 dark:border-gray-700 dark:text-red-300 text-center">
                        Acción
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                <tr class="dark:bg-gray-800 dark:border-gray-700 block md:table-row dark:hover:bg-gray-100">
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell text-left sm:text-center">
                        <span class="md:hidden font-bold dark:text-red-300">
                            ID:
                        </span> {{ $role->id }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 block md:table-cell text-left sm:text-center">
                        <span class="md:hidden font-bold dark:text-red-300">
                            Role:
                        </span> {{ $role->name }}
                    </td>
                    {{-- Action buttons --}}
                    <td class="py-0 sm:py-3 border-b border-gray-200 bg-white text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300  md:table-cell">
                        <span class="md:hidden font-bold dark:text-red-300">
                            Acción:
                        </span>
                        <div class="flex flex-row justify-center items-center gap-4">
                            {{-- @can('admin.roles.edit') --}}
                                <a class="block w-[50px] text-white-600 
                                    bg-blue-500 dark:hover:bg-blue-700 px-2 py-1 rounded dark:bg-blue-600 cursor-pointer"
                                    href="{{ route('admin.roles.edit', $role->id) }}">
                                    Editar
                                </a>                                
                            {{-- @endcan --}}
                            {{-- @can('admin.roles.destroy') --}}
                                <form method="POST" 
                                    action="{{ route('admin.roles.destroy', $role->id) }}" class="inline-block ">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-[50px] text-white-600 bg-red-500 hover:bg-red-600 px-2 py-1 rounded cursor-pointer"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar el usuario {{ $role->name }}?')">
                                        Borrar
                                    </button>
                                </form>                            
                            {{-- @endcan --}}
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @vite('resources/css/tableList.css')
        @vite('resources/js/hideTableHeaders.js')
    </div>

</x-layouts.app>