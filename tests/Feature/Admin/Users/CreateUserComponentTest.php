<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Users;

use App\Livewire\Admin\Users\CreateUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Traits\TestHelper;
use Tests\TestCase;

class CreateUserComponentTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected array $authRolesToCreateUser;
    protected array $unauthRolesToCreateUser;
    protected Collection $roles;
    protected const CREATE_USER_PERMISSION = 'admin.users.create';
    protected const ROUTE_CREATE_USER = 'admin.users.index';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->authRolesToCreateUser = $this->getAuthorizedRoles(self::CREATE_USER_PERMISSION);
        $this->unauthRolesToCreateUser = $this->getUnauthorizedRoles(self::CREATE_USER_PERMISSION);
        $this->roles = Role::all();
    }

    private function assertSeeCreateUserComponent(): void
    {
        $this->get(route(self::ROUTE_CREATE_USER))
            ->assertSeeLivewire(CreateUser::class);
    }

    private function assertDontSeeCreateUserComponent(): void
    {
        $this->get(route(self::ROUTE_CREATE_USER))
            ->assertDontSeeLivewire(CreateUser::class);
    }

    public function test_renders_the_create_user_component()
    {
        foreach ($this->authRolesToCreateUser as $authorizedRole) {
            $this->actingAsRole($authorizedRole)
                ->assertSeeCreateUserComponent();
        }
    }

    public function test_it_displays_the_create_user_form()
    {
        foreach ($this->authRolesToCreateUser as $authorizedRole) {
            $this->actingAsRole($authorizedRole)
                ->get(route(self::ROUTE_CREATE_USER))
                ->assertSee('Crear usuario')
                ->assertSee('Nombre')
                ->assertSee('Email')
                ->assertSee('Role')
                ->assertSee('-Selecciona un rol-')
                ->assertSee('super-admin')
                ->assertSee('admin')
                ->assertSee('member');
        }
    }

    private function createUserComponent(User $user, array $data = [])
    {
        return Livewire::actingAs($user)
            ->test(CreateUser::class)
            ->set('name', $data['name'] ?? '')
            ->set('email', $data['email'] ?? '')
            ->set('role', $data['role'] ?? '')
            ->set('birth_date', $data['birth_date'] ?? '');
    }

    public function test_it_can_create_a_new_user()
    {
        $userNumber = 1;
        foreach ($this->authRolesToCreateUser as $authorizedRole) {
            $this->actingAsRole($authorizedRole);
            $authorizedUser = $this->createUserAndAssignRole(roleName: $authorizedRole);
                $this->createUserComponent($authorizedUser, [
                    'name' => 'John Doe',
                    'email' => "john.doe{$userNumber}@example.com",
                    'role' => $this->roles->firstWhere('name', 'member')->id,
                    'birth_date' => now()->subYears(30)->toDateString(),
                ])
                ->call('createUser')
                ->assertHasNoErrors();

            $this->assertDatabaseHas('users', [
                'name' => 'John Doe',
                'email' => "john.doe{$userNumber}@example.com",
            ]);

            $user = User::where('email', "john.doe{$userNumber}@example.com")->first();
            $this->assertTrue($user->hasRole('member'));
            $userNumber++;
        }
    }

    #[DataProvider('invalidUserDataProvider')]
    public function test_user_creation_validation_rules(array $formData, array $expectedErrors): void
    {
        foreach ($this->authRolesToCreateUser as $authorizedRole) {
            $authorizedUser = $this->createUserAndAssignRole(roleName: $authorizedRole);
                $this->createUserComponent($authorizedUser, $formData)
                    ->call('createUser')
                    ->assertHasErrors($expectedErrors);

            $this->assertDatabaseMissing('users', [
                'name' => $formData['name'] ?? '',
                'email' => $formData['email'] ?? '',
            ]);

        }
    }

    public static function invalidUserDataProvider(): array
    {
        return [
        // name
            'name is required' => [
                'formData' => [
                    'email' => 'john@example.com',
                    'role' => 3,
                    'birth_date' => now()->subYears(20)->toDateString(),
                ],
                'expectedErrors' => ['name' => 'required'],
            ],
            'name too short' => [
                'formData' => [
                    'name' => 'John',
                    'email' => 'john@example.com',
                    'role' => 3,
                    'birth_date' => now()->subYears(20)->toDateString(),
                ],
                'expectedErrors' => ['name' => 'min'],
            ],
            'name too long' => [
                'formData' => [
                    'name' => str_repeat('a', 256),
                    'email' => 'john@example.com',
                    'role' => 3,
                    'birth_date' => now()->subYears(20)->toDateString(),
                ],
                'expectedErrors' => ['name' => 'max'],
            ],
        // email
            'email is required' => [
                'formData' => [
                    'name' => 'John Doe',
                    'role' => 3,
                    'birth_date' => now()->subYears(20)->toDateString(),
                ],
                'expectedErrors' => ['email' => 'required'],
            ],
            'email is invalid' => [
                'formData' => [
                    'name' => 'John Doe',
                    'email' => 'invalid-email',
                    'role' => 3,
                    'birth_date' => now()->subYears(20)->toDateString(),
                ],
                'expectedErrors' => ['email' => 'email'],
            ],
        // role
            'role is required' => [
                'formData' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'birth_date' => now()->subYears(20)->toDateString(),
                ],
                'expectedErrors' => ['role' => 'required'],
            ],
            'role must exist' => [
                'formData' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 999,
                    'birth_date' => now()->subYears(20)->toDateString(),
                ],
                'expectedErrors' => ['role' => 'exists'],
            ],
        // birth_date
            'birth_date is required' => [
                'formData' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 3,
                ],
                'expectedErrors' => ['birth_date' => 'required'],
            ],
            'birth_date must be a valid date' => [
                'formData' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 3,
                    'birth_date' => 'invalid-date',
                ],
                'expectedErrors' => ['birth_date' => 'date'],
            ],
            'birth_date must be after 1900-01-01' => [
                'formData' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 3,
                    'birth_date' => '1900-01-01',
                ],
                'expectedErrors' => ['birth_date' => 'after'],    
            ],
            'birth_date must be before or equal to 14 years ago' => [
                'formData' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 3,
                    'birth_date' => now()->subYears(13)->toDateString(),
                ],
                'expectedErrors' => ['birth_date' => 'before_or_equal'],
            ],
        ];
    }

    public function test_it_resets_form_after_successful_creation()
    {
        $userNumber = 1;
        foreach ($this->authRolesToCreateUser as $authorizedRole) {
            $this->actingAsRole($authorizedRole);
            $authorizedUser = $this->createUserAndAssignRole(roleName: $authorizedRole);
                $this->createUserComponent($authorizedUser, [
                    'name' => 'john Doe',
                    'email' => "john.doe{$userNumber}@example.com",
                    'role' => $this->roles->firstWhere('name', 'member')->id,
                    'birth_date' => now()->subYears(28)->toDateString(),
                ])
                ->call('createUser')
                ->assertSee('El usuario ha sido creado correctamente')
                ->assertSet('name', '')
                ->assertSet('email', '')
                ->assertSet('role', '')
                ->assertSet('birth_date', '');

            $userNumber++;   
        }
    }

    public function test_it_does_not_show_create_user_component_for_unauthorized_users()
    {
        foreach ($this->unauthRolesToCreateUser as $unauthorizedRole) {
            $this->actingAsRole($unauthorizedRole)
                ->assertDontSeeCreateUserComponent();
        }
    }

}