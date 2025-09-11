<x-layouts.app>

    @section('title', 'Editar role')
    <h1>Editar role</h1>
    <!-- form -->
    <form action="{{ route('admin.roles.update', $role) }}" method="post" class="mt-4">
        @csrf
        @method('PUT')
        @include('admin.roles.partials.form')
    </form>
</x-layouts.app>