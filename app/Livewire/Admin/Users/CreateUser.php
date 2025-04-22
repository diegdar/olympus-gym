<?php
declare(strict_types=1);

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use App\Services\CreateUserService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class CreateUser extends Component
{
    use WithFileUploads;

    public $title;

    #[Rule(['required','min:5','max:255'])]
    public string $name;
    #[Rule(['required','unique:users,email','email'])]
    public string $email;
    #[Rule(['required','exists:roles,id'])]
    public string $role;

    public Collection $roles;

    public function __construct()
    {
        if (!Auth::check() || !Auth::user()->hasPermissionTo('admin.users.create')) {
            throw new AccessDeniedHttpException('Solo los super-admin pueden acceder a este componente.');
        }
    }    

    /**
     * Creates a new user using the CreateUserService.
     *
     * This method validates the input fields using the rules defined in the class,
     * creates a new user using the CreateUserService, and flashes a success message.
     *
     * @param CreateUserService $createUserService
     */
    public function createUser(CreateUserService $createUserService): void
    {
        $validated = $this->validate();
        $validated['password'] = Hash::make('Password123!');
        $validated['role'] = $this->roles->firstWhere('id', $this->role)->name;

        $createUserService($validated);

        session()->flash('msg', 'El usuario ha sido creado correctamente');
        $this->reset(['name', 'email', 'role']);
    }

    /**
     * Renders the users component.
     *
     * This method retrieves the total count of users and all the roles
     * and passes them to the view.
     *
     * @return View
     */
    public function render(): View
    {
        $this->roles = Role::all();

        return view('livewire.admin.users.create-user', [
            'roles' => $this->roles,
        ]);
    }
}
