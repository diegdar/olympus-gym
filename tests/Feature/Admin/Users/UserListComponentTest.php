<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Users;

use App\Livewire\Admin\Users\UsersList;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Traits\TestHelper;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserListComponentTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected User $superAdminUser;

    protected array $authRolesForUsersList;
    protected array $authRolesForEditUser;
    protected array $authRolesForDeleteUser;
    protected array $unAuthRolesForUsersList;
    protected array $unauthRolesForEditUser;
    protected array $unauthRolesForDeleteUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->authRolesForUsersList = $this->getAuthorizedRoles('admin.users.index');
        $this->authRolesForEditUser = $this->getAuthorizedRoles('admin.users.edit');
        $this->authRolesForDeleteUser = $this->getAuthorizedRoles('admin.users.edit');

        $this->unAuthRolesForUsersList = $this->getUnauthorizedRoles('admin.users.index');
        $this->unauthRolesForEditUser = $this->getUnauthorizedRoles('admin.users.edit');
        $this->unauthRolesForDeleteUser = $this->getUnauthorizedRoles('admin.users.edit');

    }

    private function getUsersIndexAs(?string $roleName = null)
    {
        return $this->actingAsRole($roleName)->get(route('admin.users.index'));
    }

    public function tests_only_authorized_users_can_see_the_users_list()
    {
        foreach ($this->authRolesForUsersList as $authorizedRole) {
            $response = $this->getUsersIndexAs($authorizedRole);
            $response->assertStatus(200)
                ->assertSeeLivewire(UsersList::class)
                ->assertSee("Usuarios totales")
                ->assertSee($authorizedRole);
        }
    }

    public function test_unauthorized_users_gets_403()
    {
        foreach ($this->unAuthRolesForUsersList as $unauthorizedRole) {
            $response = $this->getUsersIndexAs($unauthorizedRole);
            $response->assertStatus(403)
                ->assertDontSeeLivewire(UsersList::class)
                ->assertDontSee('Usuarios totales');
        }
    }

    public function test_the_component_renders_correctly_with_users()
    {
        $usersNumber = 3;
        User::factory()->count($usersNumber)->create();
        foreach ($this->authRolesForUsersList as $authorizedRole) {
            $response = $this->getUsersIndexAs($authorizedRole);
            $usersNumber++;
            $response->assertStatus(200)
                ->assertSeeLivewire(UsersList::class)
                ->assertSee("Usuarios totales ({$usersNumber})")
                ->assertSee($authorizedRole);
        }
    }

    public function test_updating_search_resets_the_pagination()
    {
        User::factory()->count(15)->create();
        foreach ($this->authRolesForUsersList as $authorizedRole) {
            $authorizedUser = $this->createUserAndAssignRole(roleName: $authorizedRole);
            Livewire::actingAs($authorizedUser)
                ->test(UsersList::class)
                ->set('search', 'test')
                ->assertSet('page', null);
        }
    }
    public function test_can_filter_users_by_name()
    {
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        foreach ($this->authRolesForUsersList as $authorizedRole) {
            $authorizedUser = $this->createUserAndAssignRole(roleName: $authorizedRole);
            Livewire::actingAs($authorizedUser)
                ->test(UsersList::class)
                ->set('search', 'John')
                ->assertSee('John Doe')
                ->assertDontSee('Jane Smith');
        }
    }
    public function test_can_filter_users_by_id()
    {
        $user1 = User::factory()->create(['id' => 123]);
        $user2 = User::factory()->create(['id' => 456]);

        foreach ($this->authRolesForUsersList as $authorizedRole) {
            $authorizedUser = $this->createUserAndAssignRole(roleName: $authorizedRole);
            Livewire::actingAs($authorizedUser)
                ->test(UsersList::class)
                ->set('search', '123')
                ->assertSee($user1->name)
                ->assertDontSee($user2->name);
        }
    }

    public function test_can_filter_users_by_email()
    {
        $user1 = $this->createUserAndAssignRole(attributes: ['email' => 'john@example.com']);
        $user2 = $this->createUserAndAssignRole(attributes: ['email' => 'jane@test.com']);
        foreach ($this->authRolesForUsersList as $authorizedRole) {
            $authorizedUser = $this->createUserAndAssignRole(roleName: $authorizedRole);
            Livewire::actingAs($authorizedUser)
                ->test(UsersList::class)
                ->set('search', '@example.com')
                ->assertSee($user1->name)
                ->assertDontSee($user2->name);
        }
    }

    public function test_can_filter_users_by_role_name()
    {
        $role1 = Role::create(['name' => 'editor']);
        $role2 = Role::create(['name' => 'blogger']);

        $user1 = $this->createUserAndAssignRole($role1->name);
        $user2 = $this->createUserAndAssignRole($role2->name);

        foreach ($this->authRolesForUsersList as $authorizedRole) {
            $authorizedUser = $this->createUserAndAssignRole(roleName: $authorizedRole);
            Livewire::actingAs($authorizedUser)
                ->test(UsersList::class)
                ->set('search', $role1->name)
                ->assertSee($user1->name)
                ->assertDontSee($user2->name);
        }
    }

    public function test_can_change_the_number_of_rows_per_page()
    {
        if (file_exists('/.dockerenv')) {
            $this->markTestSkipped('Skipped in Docker environment');
            return;
        }        

        User::factory()->count(15)->create();

        foreach ($this->authRolesForUsersList as $authorizedRole) {
            $authorizedUser = $this->createUserAndAssignRole(roleName: $authorizedRole);
            Livewire::actingAs($authorizedUser)
                ->test(UsersList::class)
                ->set('numberRows', 10)
                ->assertSee(User::find(15)->name)
                ->assertDontSee(User::find(5)->name)
                ->set('numberRows', 5)
                ->assertSee(User::find(15)->name)
                ->assertDontSee(User::find(10)->name);
        }
    }

    public function test_the_updateList_method_does_nothing_but_is_present_for_event_listening()
    {
        foreach ($this->authRolesForUsersList as $authorizedRole) {
            $authorizedUser = $this->createUserAndAssignRole(roleName: $authorizedRole);
            Livewire::actingAs($authorizedUser)
                ->test(UsersList::class)
                ->call('updateList')
                ->assertOk();
        }
    }

    private function assertButtonsVisibility(array $roles, string $text, bool $shouldSee): void
    {
        foreach ($roles as $roleName) {
            $user = $this->createUserAndAssignRole(roleName: $roleName);

            $test = Livewire::actingAs($user)->test(UsersList::class);

            $shouldSee
                ? $test->assertSee($text)
                : $test->assertDontSee($text);
        }
    }


    public function test_it_displays_edit_and_delete_buttons_based_on_permissions()
    {
        $this->assertButtonsVisibility($this->authRolesForEditUser, 'Editar', shouldSee: true);
        $this->assertButtonsVisibility($this->authRolesForDeleteUser, 'Borrar', shouldSee: true);
        $this->assertButtonsVisibility($this->unauthRolesForEditUser, 'Editar', shouldSee: false);
        $this->assertButtonsVisibility($this->unauthRolesForDeleteUser, 'Borrar', shouldSee: false);
    }
}
