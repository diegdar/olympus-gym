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

        // Operational management
        Permission::create(['name' => 'admin.panel', 'description' => 'Ver el panel administrativo'])->syncRoles([$superAdmin, $admin]);

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

        // room CRUD
        Permission::create(['name' => 'rooms.index', 'description' => 'Ver listado de salas'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'rooms.create', 'description' => 'Crear una sala'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'rooms.edit', 'description' => 'Editar una sala'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'rooms.store', 'description' => 'Guardar una sala'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'rooms.destroy', 'description' => 'Eliminar una sala'])->syncRoles([$superAdmin, $admin]);

        // activitySchedule CRUD
        Permission::create(['name' => 'activities.schedule.index', 'description' => 'Ver horario de actividades'])->syncRoles([$superAdmin, $admin, $member]);
        Permission::create(['name' => 'activities.schedule.show', 'description' => 'Ver ficha horario de una actividad'])->syncRoles([$superAdmin, $admin, $member]);
  
    }
}
