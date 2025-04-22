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

        Permission::create(['name' => 'admin.users.index'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'admin.users.create'])->syncRoles([$superAdmin]);
        Permission::create(['name' => 'admin.users.edit'])->syncRoles([$superAdmin]);
        Permission::create(['name' => 'admin.users.update'])->syncRoles([$superAdmin]);
        Permission::create(['name' => 'admin.users.destroy'])->syncRoles([$superAdmin]);

    }
}
