<x-layouts.app>

    <x-slot name="title">Users</x-slot>
      
    @can('admin.users.create')
        <livewire:admin.users.create-user />        
    @endcan
    <livewire:admin.users.users-list />

</x-layouts.app>