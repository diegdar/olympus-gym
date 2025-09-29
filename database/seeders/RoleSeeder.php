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

        // Members management
        Permission::create(['name' => 'member.panel', 'description' => 'Ver el panel de los socios'])->syncRoles([$member]);
        Permission::create(['name' => 'member.subscription', 'description' => 'Ver la suscripcion del socio'])->syncRoles([$member]);
        Permission::create(['name' => 'member.change-subscription', 'description' => 'Cambiar la suscripcion del socio'])->syncRoles([$member]);

        // user CRUD
        Permission::create(['name' => 'admin.users.index', 'description' => 'Ver listado de usuarios'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'admin.users.create', 'description' => 'Ver formulario creacion de un usuario'])->syncRoles([$superAdmin]);
        Permission::create(['name' => 'admin.users.edit', 'description' => 'Editar un usuario'])->syncRoles([$superAdmin]);
        Permission::create(['name' => 'admin.users.destroy', 'description' => 'Eliminar un usuario'])->syncRoles([$superAdmin]);

        // role CRUD
        Permission::create(['name' => 'admin.roles.index', 'description' => 'Ver listado de roles'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'admin.roles.create', 'description' => 'Ver formulario creacion de un role'])->syncRoles([$superAdmin]);
        Permission::create(['name' => 'admin.roles.edit', 'description' => 'Editar/ver un role'])->syncRoles([$superAdmin]);
        Permission::create(['name' => 'admin.roles.destroy', 'description' => 'Eliminar un role'])->syncRoles([$superAdmin]);

        // room CRUD
        Permission::create(['name' => 'rooms.index', 'description' => 'Ver listado de salas'])->syncRoles([ $admin, $member]);
        permission::create(['name' => 'rooms.show', 'description' => 'Ver ficha de una sala'])->syncRoles([$admin, $member]);
        Permission::create(['name' => 'rooms.create', 'description' => 'Ver formulario creacion de una sala'])->syncRoles([$admin]);
        Permission::create(['name' => 'rooms.edit', 'description' => 'Editar una sala'])->syncRoles([$admin]);
        Permission::create(['name' => 'rooms.store', 'description' => 'Guardar una sala'])->syncRoles([$admin]);
        Permission::create(['name' => 'rooms.destroy', 'description' => 'Eliminar una sala'])->syncRoles([$admin]);

        // activity CRUD
        Permission::create(['name' => 'activities.index', 'description' => 'Ver listado de actividades'])->syncRoles([$admin, $member]);
        Permission::create(['name' => 'activities.show', 'description' => 'Ver ficha de una actividad'])->syncRoles([$admin, $member]);
        Permission::create(['name' => 'activities.create', 'description' => 'Ver formulario creacion de una actividad'])->syncRoles([$admin]);
        Permission::create(['name' => 'activities.edit', 'description' => 'Editar una actividad'])->syncRoles([$admin]);
        Permission::create(['name' => 'activities.store', 'description' => 'Guardar una actividad'])->syncRoles([$admin]);
        Permission::create(['name' => 'activities.destroy', 'description' => 'Eliminar una actividad'])->syncRoles([$admin]);

        // activitySchedule
        Permission::create(['name' => 'activity.schedules.index', 'description' => 'Ver horario de actividades'])->syncRoles([$admin, $member]);
        Permission::create(['name' => 'activity.schedules.show', 'description' => 'Ver ficha horario de una actividad'])->syncRoles([$admin, $member]);
        Permission::create(['name' => 'activity.schedules.create', 'description' => 'Ver formulario creacion de un horario para una actividad'])->syncRoles([$admin]);
        Permission::create(['name' => 'activity.schedules.store', 'description' => 'Guardar horario para una actividad'])->syncRoles([$admin]);
        Permission::create(['name' => 'activity.schedules.edit', 'description' => 'Editar horario de una actividad'])->syncRoles([$admin]);
        Permission::create(['name' => 'activity.schedules.destroy', 'description' => 'Eliminar horario de una actividad'])->syncRoles([$admin]);
        Permission::create(['name' => 'user.reservations', 'description' => 'Ver mis reservas'])->syncRoles([$member]);

        // Enroll in activity schedule
        Permission::create(['name' => 'activity.schedules.enroll', 'description' => 'Inscribirse a una actividad'])->syncRoles([$member]);
        Permission::create(['name' => 'activity.schedules.unenroll', 'description' => 'Desinscribirse de una actividad'])->syncRoles([$member]);

        // Manage attendance in activity schedule
        Permission::create(['name' => 'activity.schedules.enrolled-users', 'description' => 'Ver listado de usuarios inscritos en el horario de una actividad'])->syncRoles([$admin]);
        Permission::create(['name' => 'activity.schedules.attendance', 'description' => 'Gestionar asistencia en el horario de una actividad'])->syncRoles([$admin]);

        // subscription statistics
        Permission::create(['name' => 'admin.subscriptions.stats', 'description' => 'Ver estadísticas de suscripciones'])
            ->syncRoles([$superAdmin, $admin]);
    }
}
