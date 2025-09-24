<x-layouts.app>
    @section('title', 'Crear sala')
    <h1 class="mb-3 text-2xl font-bold mt-2 mx-2 text-2nd">Crear sala</h1>
    <!-- form -->
    <form action="{{ route('rooms.store') }}" method="post" class="mt-4">
        @csrf
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
            <div class="flex flex-wrap justify-start gap-4">
                <!-- nombre -->
                <article class="flex flex-col sm:flex-wrap gap-2">
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
                <article class="flex flex-col sm:flex-wrap gap-2 w-full">
                    <label for="description" 
                        class="font-bold sm:text-xl">
                        Descripcion:
                    </label>
                    <div class="flex flex-col mr-2">
                        <textarea name="description"
                            class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300
                            min-h-[400px]"
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
