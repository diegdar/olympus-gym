<div>
    {{-- name and submit boton --}}
    <section class="flex justify-center items-center flex-wrap sm:flex-nowrap ">
        <!-- nombre -->
        <div class="mr-2">
            <article class="flex items-center">
                <label for="name" class="font-bold ml-4 mr-2">Nombre:</label>
                <div class="flex flex-col">
                    <input name="name"
                        class="w-full shadow 
                        appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                        type="text" placeholder="Ingrese nombre del role" id="name" value="{{ old('name', $role->name ?? '') }}">
                    @error('name')
                        <span class="text-red-500 text-sm
                                dark:text-red-400">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </article>
        </div>
        <!-- submit boton -->
        <div class="flex-col text-center 2xs:mt-2 s:mt-0">
            <button type="submit"
                class="bg-blue-500 
                hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-blue-600 dark:hover:bg-blue-700 cursor-pointer">
                Guardar
            </button>
        </div>
    </section>
    {{-- permissions list --}}
    <section
        class="mx-2 my-4 px-5 py-3 border-b-2 border-gray-200 
    bg-gray-100 text-left text-lg dark:bg-gray-900 dark:border-gray-700">
        <h2 class="mb-3">Lista de permisos:</h2>
        @error('permissions')
            <span class="text-red-500 text-sm
            dark:text-red-400">
                {{ $message }}
            </span>
        @enderror
        <!-- form roles assignation -->
        @foreach ($permissions as $permission)
            <div>
                <input type="checkbox" name="permissions[]" id="{{ $permission->id }}" 
                    value="{{ $permission->id }}" 
                    @if (isset($role) && $role->hasPermissionTo($permission->id)) 
                        checked 
                    @endif
                >
                <label for="{{ $permission->id }}" class="ml-2 font-bold">
                    {{ $permission->description }}
                </label>
            </div>
        @endforeach
    </section>
</div>
