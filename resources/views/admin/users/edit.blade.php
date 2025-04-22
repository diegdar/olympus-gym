<x-layouts.app>
    <x-slot name="title">Editar usuario</x-slot>
    <div class=" mx-4 my-4 px-5 py-3 border-b-2 border-gray-200 
        bg-gray-100 text-left text-lg dark:bg-gray-900 dark:border-gray-700">
        <div class="flex align-center">
            <p class="pr-2 bg-gray-100
            text-left font-bold text-lg font-bold dark:bg-gray-900 dark:border-gray-700 dark:text-red-300">Nombre:</p>
            <p class="pl-2 font-bold">{{ $user->name }}</p>
        </div>
        <h5>Roles:</h5>
        <!-- form roles assignation -->
        <form action="{{ route('admin.users.update', $user) }}" method="post">
            @csrf
            @method('PUT')
            @foreach ($roles as $role)
                <div>
                    <input type="checkbox" name="roles[]" id="{{ $role->id }}" value="{{ $role->id }}"
                        {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                    <label for="{{ $role->id }}">{{ $role->name }}</label>
                </div>
            @endforeach
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-3">
                Guardar
            </button>
        </form>
    </div>

</x-layouts.app>