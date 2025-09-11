<x-layouts.app>

    @section('title', 'Ver usuarios')
      
    @can('admin.users.create')
        <livewire:admin.users.create-user />        
    @endcan
    <livewire:admin.users.users-list />

</x-layouts.app>