<?php
declare(strict_types=1);

namespace Tests\Feature\Admin\Users;

use App\Livewire\Admin\Users\UsersList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserListComponentTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create(['name' => 'admin.users.index', 'description' => 'Ver pagina de listado de usuarios']);
        Permission::create(['name' => 'admin.users.edit', 'description' => 'Editar un usuario']);
        Permission::create(['name' => 'admin.users.destroy', 'description' => 'Eliminar un usuario']);

        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(['admin.users.index', 'admin.users.edit', 'admin.users.destroy']);        

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(['admin.users.index']);

        $memberRole = Role::create(['name' => 'member']);

        $this->superAdminUser = User::factory()->create()->assignRole('super-admin');
     
    }

    public function test_only_admins_and_super_admins_can_see_the_component()
    {
        Livewire::actingAs($this->superAdminUser);
        $this->get(route('admin.users.index'))
            ->assertSeeLivewire(UsersList::class);

        $adminUser = User::factory()->create()->assignRole('admin');
        Livewire::actingAs($adminUser);
        $this->get(route('admin.users.index'))
            ->assertSeeLivewire(UsersList::class);

        $memberUser = User::factory()->create()->assignRole('member');
        Livewire::actingAs($memberUser);
        $this->get(route('admin.users.index'))
            ->assertDontSeeLivewire(UsersList::class);
    }

    public function test_the_component_renders_correctly_with_users()
    {
        Livewire::actingAs($this->superAdminUser);
        User::factory()->count(3)->create();


        $this->get(route('admin.users.index'))
            ->assertSeeLivewire(UsersList::class)
            ->assertSee('Listado de usuarios (4)') 
            ->assertSee($this->superAdminUser->name);
    }

    public function test_updating_search_resets_the_pagination()
    {        
        User::factory()->count(12)->create();
        
        Livewire::actingAs($this->superAdminUser)
            ->test(UsersList::class)
            ->set('search', 'test')
            ->assertSet('page', null);
    }

    public function test_can_filter_users_by_name()
    {
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        Livewire::actingAs($this->superAdminUser)
            ->test(UsersList::class)
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    public function test_can_filter_users_by_id()
    {
        $user1 = User::factory()->create(['id' => 123]);
        $user2 = User::factory()->create(['id' => 456]);

        Livewire::actingAs($this->superAdminUser)
            ->test(UsersList::class)
            ->set('search', '123')
            ->assertSee($user1->name)
            ->assertDontSee($user2->name);
    }

    public function test_can_filter_users_by_email()
    {
        $user1 = User::factory()->create(['email' => 'john@example.com']);
        $user2 = User::factory()->create(['email' => 'jane@example.com']);

        Livewire::actingAs($this->superAdminUser)
            ->test(UsersList::class)
            ->set('search', 'john@')
            ->assertSee($user1->name)
            ->assertDontSee($user2->name);
    }

    public function test_can_filter_users_by_role_name()
    {
        $editorRole = Role::create(['name' => 'editor']);
        $user1 = User::factory()->create();
        $user1->assignRole('editor');
        $user2 = User::factory()->create();
        $user2->assignRole('admin');

        Livewire::actingAs($this->superAdminUser)
            ->test(UsersList::class)
            ->set('search', 'editor')
            ->assertSee($user1->name)
            ->assertDontSee($user2->name);
    }

    public function test_can_change_the_number_of_rows_per_page()
    {
        User::factory()->count(15)->create();

        Livewire::actingAs($this->superAdminUser)
            ->test(UsersList::class)
            ->assertSee(User::find(15)->name)
            ->assertSee(User::find(2)->name)
            ->set('numberRows', 10)
            ->assertSee(User::find(15)->name)
            ->assertDontSee(User::find(5)->name)
            ->set('numberRows', 5)
            ->assertSee(User::find(15)->name)
            ->assertDontSee(User::find(10)->name);
    }

    public function test_the_updateList_method_does_nothing_but_is_present_for_event_listening()
    {
        Livewire::actingAs($this->superAdminUser)
            ->test(UsersList::class)
            ->call('updateList')
            ->assertOk();
    }

    public function test_it_displays_edit_and_delete_buttons_based_on_permissions()
    {
        $user = User::factory()->create();

        Livewire::actingAs($this->superAdminUser)
            ->test(UsersList::class)
            ->assertSee('Editar')
            ->assertSee('Borrar');

        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        Livewire::actingAs($adminUser)
            ->test(UsersList::class)
            ->assertDontSee('Editar')
            ->assertDontSee('Borrar');

        $memberRole = User::factory()->create();
        $memberRole->assignRole('member');
        Livewire::actingAs($memberRole)
            ->test(UsersList::class)
            ->assertDontSee('Editar')
            ->assertDontSee('Borrar');
    }
}
