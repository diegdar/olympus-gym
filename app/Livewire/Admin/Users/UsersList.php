<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UsersList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $numberRows = 15;
    public int $usersCount;

    public function __construct()
    {
        if (!Auth::check() || !Auth::user()->hasPermissionTo('admin.users.index')) {
            throw new AccessDeniedHttpException('Solo los super-admin pueden acceder a este componente.');
        }
        $this->usersCount = User::count();
    }

    /**
     * When the user is created, the list of users is updated.
     * This method is called by the `create_user` event.
     *
     * @param  User|null  $user
     * @return void
     */
    #[On('create_user')]
    public function updateList($user = null): void
    {
    }

    /**
     * When the search field is updated, the pagination is reset.
     *
     * This method is called automatically by Livewire when the `search` property
     * is updated. It resets the pagination so that the first page of results is
     * returned. This is useful when the user is searching for a specific register 
     * that is in a different page than they are.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * This computed property returns a paginated list of users.
     *
     * It filters the users by the `search` property and paginates the results.
     * The filter is done by looking for the `search` string in the user's
     * `name`, `id`, `email` and all the roles the user has.
     *
     * The results are ordered by `id` in descending order.
     * 
     * @return Collection|LengthAwarePaginator
     */
    #[Computed()]
    public function users():Collection|LengthAwarePaginator
    {
        $search = trim($this->search);
        return User::where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        })->orWhereHas('roles', function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->orderBy('id', 'desc')
        ->paginate($this->numberRows);
    }

    /**
     * Render the view for the users list component.
     *
     * @return View
     */
    public function render(): View
    {        
        return view('livewire.admin.users.users-list', [
            'usersCount' => User::count(),
            'users' => $this->users(),
        ]);
    }

}
