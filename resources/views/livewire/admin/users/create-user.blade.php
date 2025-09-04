<div wire:poll.5s class="w-full mx-auto dark:bg-gray-800 dark:text-gray-100 p-0">
    <div class="flex flex-wrap">
        <!-- title -->
        <h1 class="text-center w-full text-2nd text-3xl dark:text-gray-100">Usuarios </h1>
        <hr class="w-full border-t border-gray-300 my-2 dark:border-gray-700">
        <div class="w-full flex flex-col">
            <!-- create user -->
            <div class="w-full px-4">
                {{-- Mensaje de alerta --}}
                @if (session('msg'))
                    <div class="w-full bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-800 dark:border-green-700 dark:text-green-200 text-center" role="alert">
                        <span class="block sm:inline font-bold">
                            {{ session('msg') }}
                        </span>
                    </div>
                @endif 
                <h2 class="text-start text-2xl dark:text-gray-100 mb-2">Crear usuario</h2>
                <!-- form -->
                <form wire:submit.prevent='createUser' >
                    @csrf
                    <section class="flex flex-wrap items-start justify-start sm:items-center sm:justify-center">
                        <!-- nombre -->
                        <div class="mt-2 mb-flex flex-col">
                            @error('name')
                                <span
                                    class="text-red-500 text-sm
                                        dark:text-red-400">{{ $message }}
                                </span>
                            @enderror                            
                            <article class="flex flex-col">
                                <input wire:model="name"
                                    class="mr-2 mb-3 shadow appearance-none border
                                    rounded py-2 px-3 text-gray-700 
                                    leading-tight focus:outline-none 
                                    dark:bg-gray-700 dark:border-gray-600 
                                    dark:text-gray-300"
                                    type="text" placeholder="Nombre" id="name"
                                    autocomplete="name">
                            </article>
                        </div>
                        <!-- email -->
                        <div class="mt-2 mb-flex flex-col">
                            @error('email')
                                <span
                                    class="text-red-500 text-sm
                                        dark:text-red-400">{{ $message }}
                                </span>
                            @enderror                            
                            <article class="flex flex-col">
                                <input wire:model="email"
                                    class="mr-2 mb-3 shadow appearance-none border
                                        rounded py-2 px-3 text-gray-700 
                                        leading-tight focus:outline-none 
                                        dark:bg-gray-700 dark:border-gray-600 
                                        dark:text-gray-300"
                                    type="text" placeholder="Email" id="email"
                                    autocomplete="email">
                            </article>
                        </div>
                        <!-- role -->
                        <div class="mt-2 mb-flex flex-col">
                            @error('role')
                                    <span
                                        class="text-red-500 text-sm
                                                dark:text-red-400">{{ $message }}
                                    </span>
                                @enderror                            
                            <article class="flex flex-col">
                                <select wire:model="role"
                                    class="mr-2 mb-3 shadow 
                                        appearance-none border
                                        rounded py-2 px-3 text-gray-700 
                                        leading-tight focus:outline-none 
                                        dark:bg-gray-700 dark:border-gray-600 
                                        dark:text-gray-300 cursor-pointer"
                                    type="text"
                                    placeholder="Role" id="role">
                                    <option value="" >
                                        -Selecciona un rol-
                                    </option>
                                    @foreach ($roles as $role )
                                        <option value="{{ $role->id }}">
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </article>
                        </div>
                        <!-- birth_date -->
                        <div class="mt-2 mb-flex flex-col">
                            @error('birth_date')
                                <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                            @enderror
                            <article class="flex flex-col">
                                <input wire:model="birth_date"
                                    class="mr-2 mb-3 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                    type="date" id="birth_date" max="{{ now()->toDateString() }}" placeholder="Fecha nacimiento">
                            </article>
                        </div>
                    </section>
                    <!-- submit boton -->
                    <div class="flex-col text-center mb-4">
                        <button wire:loading.attr='disabled' wire:target='createUser'
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 
                                px-4 rounded focus:outline-none focus:shadow-outline 
                                dark:bg-blue-600 dark:hover:bg-blue-700 cursor-pointer">
                            Crear usuario
                        </button>
                        <div wire:loading wire:target='createUser' class="mt-2 text-blue-500 dark:text-blue-400">
                            Enviando...
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>