<?php
declare(strict_types=1);

namespace App\Livewire\Admin\Users;

use Illuminate\Database\Eloquent\Collection;
use App\Services\CreateUserService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Component
{
    use WithFileUploads;

    public $title;

    public string $name;
    public string $email;
    public string $role;
    public string $birth_date;

    public Collection $roles;  


    public function getRules(): array
    {
        return [
            'name' => ['required','min:5','max:255'],
            'email' => ['required','unique:users,email','email'],
            'role' => ['required','exists:roles,id'],
            'birth_date' => ['required','date','after:1900-01-01','before_or_equal:' 
                . now()->subYears(14)->toDateString()
            ],
        ];
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
    $this->reset(['name', 'email', 'role', 'birth_date']);
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
