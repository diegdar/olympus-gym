<?php
declare(strict_types=1);

namespace Tests\Helpers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

trait RoleTestHelper
{

    /**
     * Get an array of authorized roles for a given permission name.
     *
     * @param string $permissionName
     * @return array
     */
    public function getAuthorizedRoles(string $permissionName): array
    {
        $permission = Permission::where('name', $permissionName)->first();
        return $permission->roles->pluck('name')->toArray();
    }

    /**
     * Get an array of unauthorized roles for a given permission name.
     *
     * This function retrieves all roles that do not have the specified permission
     * assigned to them. It returns an array of role names that lack the given permission.
     *
     * @param string $permissionName The name of the permission to check against.
     * @return array An array of role names that do not have the specified permission.
     */
    public function getUnauthorizedRoles(string $permissionName): array
    {
        $permission = Permission::where('name', $permissionName)->first();
        return Role::whereDoesntHave('permissions', function ($query) use ($permission) {
            $query->where('id', $permission->id);
        })->get()->pluck('name')->toArray();
    }


    /**
     * Perform an action as a user with the specified role.
     *
     * @param string $roleName The name of the role to assign to the user.
     * @return self The test case instance.
     */
    public function actingAsRole(string $roleName): self
    {
        $user = User::factory()->create()->assignRole($roleName);
        return $this->actingAs($user);
    }    
}