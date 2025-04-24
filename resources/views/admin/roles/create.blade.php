<x-layouts.app>

    <x-slot name="title">Crear roles</x-slot>
    <h1>Crear role</h1>
    <!-- form -->
    <form action="{{ route('admin.roles.store') }}" method="post" class="mt-4">
        @csrf
        
        @include('admin.roles.partials.form')
    </form>
</x-layouts.app>
