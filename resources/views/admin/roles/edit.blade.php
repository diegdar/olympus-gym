<x-layouts.app>

    <x-slot name="title">Editar roles</x-slot>
    <h1>Editar role</h1>
    <!-- form -->
    <form action="{{ route('admin.roles.update', $role) }}" method="post" class="mt-4">
        @csrf
        @method('PUT')
        @include('admin.roles.partials.form')
    </form>
</x-layouts.app>