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
            ->set('role', $data['role'] ?? '');
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
            'name is required' => [
                'formData' => [
                    'email' => 'john@example.com',
                    'role' => 3,
                ],
                'expectedErrors' => ['name' => 'required'],
            ],
            'name too short' => [
                'formData' => [
                    'name' => 'John',
                    'email' => 'john@example.com',
                    'role' => 3,
                ],
                'expectedErrors' => ['name' => 'min'],
            ],
            'name too long' => [
                'formData' => [
                    'name' => str_repeat('a', 256),
                    'email' => 'john@example.com',
                    'role' => 3,
                ],
                'expectedErrors' => ['name' => 'max'],
            ],
            'email is required' => [
                'formData' => [
                    'name' => 'John Doe',
                    'role' => 3,
                ],
                'expectedErrors' => ['email' => 'required'],
            ],
            'email is invalid' => [
                'formData' => [
                    'name' => 'John Doe',
                    'email' => 'invalid-email',
                    'role' => 3,
                ],
                'expectedErrors' => ['email' => 'email'],
            ],
            'role is required' => [
                'formData' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
                'expectedErrors' => ['role' => 'required'],
            ],
            'role must exist' => [
                'formData' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 999,
                ],
                'expectedErrors' => ['role' => 'exists'],
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
                ])
                ->call('createUser')
                ->assertSee('El usuario ha sido creado correctamente')
                ->assertSet('name', '')
                ->assertSet('email', '')
                ->assertSet('role', '');

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