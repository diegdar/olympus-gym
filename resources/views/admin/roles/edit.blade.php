<x-layouts.app>

    @section('title', 'Editar role')
    <h1 class="mb-3 text-2xl font-bold mt-2 mx-2 text-2nd">Editar role</h1>
    <!-- form -->
    <form action="{{ route('admin.roles.update', $role) }}" method="post" class="mt-4">
        @csrf
        @method('PUT')
        @include('admin.roles.partials.form')
    </form>
</x-layouts.app>