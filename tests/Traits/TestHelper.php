<?php
declare(strict_types=1);

namespace Tests\Traits;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Subscription;

trait TestHelper
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
     * Creates a new user and assigns the specified role to the user.
     *
     * @param string $roleName The name of the role to assign to the user. Defaults to 'member'.
     * @return User The created user with the assigned role.
     */
    public function createUser(string $roleName = 'member'): User
    {
        return User::factory()
                ->create()->assignRole($roleName);
    }

    /**
     * Perform an action as a user with the specified role.
     *
     * @param string $roleName The name of the role to assign to the user.
     * @return self The test case instance.
     */
    public function actingAsRole(string $roleName): self
    {
        $user = User::factory()
                 ->create()
                    ->assignRole($roleName);
        return $this->actingAs($user);
    }

    /**
     * Create User and sign in as that user.
     *
     * @param string $roleName The name of the role to assign to the user.
     * @param array $attributes The attributes to create the user with.
     * @return User The created user.
     */
    public function createUserAndSignIn(string $roleName = 'member', array $attributes = []): User
    {
        $user = User::factory()
                 ->create($attributes)
                    ->assignRole($roleName);
        $this->actingAs($user);
        return $user;
    }

    /**
     * Create a user with a 'member' role and assign a subscription to the user.
     *
     * The subscription's start date is set to the current date and time. The end date is
     * calculated by adding the duration of the subscription's fee to the start date. The
     * subscription's status will be set to 'active' by default, but can be changed by
     * passing a different value to the $status parameter.
     *
     * @param string $status The status to assign to the subscription. Defaults to 'active'.
     * @return array An array containing the created user, the assigned subscription, the start date,
     * and the end date.
     */
    public function createSubscription(string $status = 'active'): array
    {
        $subscription = Subscription::where('fee', 'monthly')->first();
        $user = $this->createUserAndSignIn('member');

        $startDate = now();
        $endDate = $startDate->copy()->addMonths($subscription->duration);

        $user->subscriptions()->attach($subscription->id, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'payment_date' => now(),
            'status' => $status
        ]);

        return [$user, $subscription, $startDate, $endDate];
    }


}
