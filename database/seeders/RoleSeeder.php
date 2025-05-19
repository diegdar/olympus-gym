<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $member = Role::firstOrCreate(['name' => 'member']);
        $guest = Role::firstOrCreate(['name' => 'guest']);

        // user CRUD
        Permission::create(['name' => 'admin.users.index', 'description' => 'Ver listado de usuarios'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'admin.users.create', 'description' => 'Crear un usuario'])->syncRoles([$superAdmin]);
        Permission::create(['name' => 'admin.users.edit', 'description' => 'Editar un usuario'])->syncRoles([$superAdmin]);
        Permission::create(['name' => 'admin.users.destroy', 'description' => 'Eliminar un usuario'])->syncRoles([$superAdmin]);

        // role CRUD
        Permission::create(['name' => 'admin.roles.index', 'description' => 'Ver listado de roles'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'admin.roles.create', 'description' => 'Crear un role'])->syncRoles([$superAdmin]);
        Permission::create(['name' => 'admin.roles.edit', 'description' => 'Editar/ver un role'])->syncRoles([$superAdmin]);
        Permission::create(['name' => 'admin.roles.destroy', 'description' => 'Eliminar un role'])->syncRoles([$superAdmin]);

        // activity CRUD
        Permission::create(['name' => 'activities.index', 'description' => 'Ver listado de actividades'])->syncRoles([$superAdmin, $admin, $member]);
  
    }
}
