<x-layouts.app>

    <x-slot name="title">Editar sala</x-slot>
    <h1>Editar sala</h1>
    <!-- form -->
    <form action="{{ route('rooms.update', $room) }}" method="post" class="mt-4">
        @csrf
        @method('PUT')

        {{-- name, description and submit boton --}}
        <section class="ml-3">
            <!-- submit boton -->
            <div class="flex-col text-center mb-5 mt-8">
                <button type="submit"
                    class="bg-blue-500 
                hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-blue-600 dark:hover:bg-blue-700 cursor-pointer">
                    Guardar
                </button>
            </div>
            {{-- form fields --}}
            <div class="flex flex-wrap justify-center gap-4">
                <!-- nombre -->
                <article class="flex flex-wrap gap-2">
                    <label for="name" class="font-bold sm:text-xl">Nombre:</label>
                    <div class="flex flex-col">
                        <input name="name"
                            class="w-full shadow 
                        appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                            type="text" placeholder="nombre de la sala" id="name"
                            value="{{ old('name', $room->name ?? '') }}">
                        @error('name')
                            <span class="text-red-500 text-sm
                                dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </article>
                <!-- descripcion -->
                <article class="flex flex-wrap gap-2">
                    <label for="description" 
                        class="font-bold sm:text-xl">
                        Descripcion:
                    </label>
                    <div class="flex flex-col">
                        <textarea name="description"
                            class="w-full shadow appearance-none 
                            border rounded py-2 px-3 text-gray-
                            700 leading-tight focus:outline-none 
                            dark:bg-gray-700 dark:border-gray-600 
                            dark:text-gray-300
                            min-h-[125px]
                            min-w-[250px]"
                            id="description" placeholder="Descripción de la sala">{{ old('description', $room->description ?? '') }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-sm dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </article>
            </div>
        </section>

    </form>
</x-layouts.app>
