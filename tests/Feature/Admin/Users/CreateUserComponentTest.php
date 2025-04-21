<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Users;

use App\Livewire\Admin\Users\CreateUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CreateUserComponentTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdminUser;
    protected Role $adminRole;
    protected Role $memberRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->superAdminUser = User::factory()->create()->assignRole('super-admin');
        $this->adminRole = Role::findByName('admin');
        $this->memberRole = Role::findByName('member');
    }

    private function actingAsSuperAdmin(): self
    {
        return $this->actingAs($this->superAdminUser);
    }

    private function assertSeeCreateUserComponent(): void
    {
        $this->get(route('admin.users.index'))
            ->assertSeeLivewire(CreateUser::class);
    }

    private function assertDontSeeCreateUserComponent(): void
    {
        $this->get(route('admin.users.index'))
            ->assertDontSeeLivewire(CreateUser::class);
    }

    private function createUserComponent(array $data = [])
    {
        return Livewire::actingAs($this->superAdminUser)
            ->test(CreateUser::class)
            ->set('name', $data['name'] ?? '')
            ->set('email', $data['email'] ?? '')
            ->set('role', $data['role'] ?? '');
    }

    public function test_renders_the_create_user_component()
    {
        $this->actingAsSuperAdmin()
            ->assertSeeCreateUserComponent();
    }

    public function test_it_displays_the_create_user_form()
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.users.index'))
            ->assertSee('Crear usuario')
            ->assertSee('Nombre')
            ->assertSee('Email')
            ->assertSee('Role')
            ->assertSee('-Selecciona un rol-')
            ->assertSee('super-admin')
            ->assertSee('admin')
            ->assertSee('member');
    }

    public function test_it_can_create_a_new_user()
    {
        $this->actingAsSuperAdmin();

        $this->createUserComponent([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'role' => $this->adminRole->id,
        ])
            ->call('createUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_it_requires_name()
    {
        $this->actingAsSuperAdmin();

        $this->createUserComponent([
            'email' => 'john.doe@example.com',
            'role' => $this->adminRole->id,
        ])
            ->call('createUser')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_name_must_be_at_least_5_characters()
    {
        $this->actingAsSuperAdmin();

        $this->createUserComponent([
            'name' => 'John',
            'email' => 'john.doe@example.com',
            'role' => $this->adminRole->id,
        ])
            ->call('createUser')
            ->assertHasErrors(['name' => 'min']);
    }

    public function test_name_must_not_be_longer_than_255_characters()
    {
        $this->actingAsSuperAdmin();
        $longName = str_repeat('a', 256);

        $this->createUserComponent([
            'name' => $longName,
            'email' => 'john.doe@example.com',
            'role' => $this->adminRole->id,
        ])
            ->call('createUser')
            ->assertHasErrors(['name' => 'max']);
    }

    public function test_it_requires_email()
    {
        $this->actingAsSuperAdmin();

        $this->createUserComponent([
            'name' => 'John Doe',
            'role' => $this->adminRole->id,
        ])
            ->call('createUser')
            ->assertHasErrors(['email' => 'required']);
    }

    public function test_email_must_be_a_valid_email_address()
    {
        $this->actingAsSuperAdmin();

        $this->createUserComponent([
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'role' => $this->adminRole->id,
        ])
            ->call('createUser')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_email_must_be_unique()
    {
        $this->actingAsSuperAdmin();
        User::factory()->create(['email' => 'existing@example.com']);

        $this->createUserComponent([
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'role' => $this->adminRole->id,
        ])
            ->call('createUser')
            ->assertHasErrors(['email' => 'unique']);
    }

    public function test_it_requires_a_role()
    {
        $this->actingAsSuperAdmin();

        $this->createUserComponent([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ])
            ->call('createUser')
            ->assertHasErrors(['role' => 'required']);
    }

    public function test_role_must_exist_in_roles_table()
    {
        $this->actingAsSuperAdmin();

        $this->createUserComponent([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'role' => 999,
        ])
            ->call('createUser')
            ->assertHasErrors(['role' => 'exists']);
    }

    public function test_it_resets_form_after_successful_creation()
    {
        $this->actingAsSuperAdmin();

        $this->createUserComponent([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'role' => $this->adminRole->id,
        ])
            ->call('createUser')
            ->assertSee('El usuario ha sido creado correctamente')
            ->assertSet('name', '')
            ->assertSet('email', '')
            ->assertSet('role', '');
    }

    public function test_it_does_not_show_create_user_component_for_non_admin_users()
    {
        $this->actingAs(User::factory()->create()->assignRole('admin'))
            ->assertDontSeeCreateUserComponent();

        $this->actingAs(User::factory()->create()->assignRole('member'))
            ->assertDontSeeCreateUserComponent();
    }

}