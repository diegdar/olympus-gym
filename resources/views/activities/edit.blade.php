<x-layouts.app>

    <x-slot name="title">Editar actividad</x-slot>
    <h1>Editar actividad</h1>
    <!-- form -->
    <form action="{{ route('activities.update', $activity) }}" method="post" class="mt-4">
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
            <div class="flex flex-wrap justify-start 
                sm:justify-center gap-4">
                <!-- name -->
                <article class="flex flex-col sm:flex-wrap gap-2">
                    <label for="name" class="font-bold sm:text-xl">Nombre:</label>
                    <div class="flex flex-col">
                        <input name="name"
                            class="w-full shadow 
                        appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                            type="text" placeholder="nombre de la actividad" id="name"
                            value="{{ old('name', $activity->name ?? '') }}">
                        @error('name')
                            <span class="text-red-500 text-sm
                                dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </article>
                <!-- duration -->
                <article class="flex flex-col sm:flex-wrap gap-2">
                    <label for="duration" class="font-bold sm:text-xl">Duración:</label>
                    <div class="flex flex-col">
                        <input name="duration"
                            class="w-full shadow 
                        appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                            type="text" placeholder="duracion de la actividad" id="duration"
                            value="{{ old('duration', $activity->duration ?? '') }}"
                            min="30" step="15">
                        @error('duration')
                            <span class="text-red-500 text-sm
                                dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </article>
                <!-- description -->
                <article class="flex flex-col sm:flex-wrap gap-2 w-full">
                    <label for="description" 
                        class="font-bold sm:text-xl">
                        Descripcion:
                    </label>
                    <div class="flex flex-col mr-2">
                        @error('description')
                            <span class="text-red-500 text-sm dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror                        
                        <textarea name="description"
                            class="w-full shadow appearance-none 
                            border rounded py-2 px-3 text-gray-
                            700 leading-tight focus:outline-none 
                            dark:bg-gray-700 dark:border-gray-600 
                            dark:text-gray-300
                            min-h-[400px]"
                            id="description" placeholder="Descripción de la actividad">{{ old('description', $activity->description ?? '') }}
                        </textarea>
                    </div>
                </article>
            </div>
        </section>
    </form>
</x-layouts.app>
