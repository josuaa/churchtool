<?php

namespace App\Providers;

use App\Models\User\Capability;
use App\Models\User\Group;
use App\Models\User\User;
use Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies();

        /**
         * Check if user is allowed to access an arbitrary user object.
         */
        Gate::define(
            'get-user',
            function (User $user, User $subject = null) {
                if (!is_null($subject) and $subject->id == $user->id) {
                    return true;
                }
                return $user->hasCapability(Capability::VIEW_USER_NAMES);
            }
        );

        /**
         * Check if user is allowed to update a user object (excluding group memberships)
         */
        Gate::define(
            'update-user',
            function (User $user, User $subject) {
                if ($subject->id == $user->id) {
                    return true;
                }

                return $user->hasCapability(Capability::MANAGE_USERS);
            }
        );

        /**
         * Check if user is allowed to see a user's email adress.
         * @todo Maybe we should in general allow a user the see the subjects email adress if they belong to the same group..?
         */
        Gate::define(
            'get-user-email',
            function (User $user, User $subject) {
                return $user->hasCapability(Capability::VIEW_USER_ADDRESS_DATA);
            }
        );

        /**
         * Check if user is allowed to see a user's date of birth
         */
        Gate::define(
            'get-user-dateofbirth',
            function (User $user, User $subject) {
                return $user->hasCapability(Capability::VIEW_USER_DATE_OF_BIRTH);
            }
        );

        /**
         * Check if user is allowed to create a user.
         */
        Gate::define(
            'create-user',
            function (User $user) {
                return $user->hasCapability(Capability::MANAGE_USERS);
            }
        );

        /**
         * Check if user is allowed to delete a user.
         */
        Gate::define(
            'delete-user',
            function (User $user) {
                return $user->hasCapability(Capability::MANAGE_USERS);
            }
        );

        /**
         * Check if user is allowed to view a group.
         */
        Gate::define(
            'get-group',
            function (User $user) {
                return $user->hasCapability(Capability::VIEW_GROUPS);
            }
        );

        /**
         * Check if user is allowed to create and update a group.
         */
        Gate::define(
            'create-update-group',
            function (User $user) {
                return $user->hasCapability(Capability::MANAGE_GROUPS);
            }
        );

        /**
         * Check if user is allowed to create and update a group.
         */
        Gate::define(
            'delete-group',
            function (User $user) {
                return $user->hasCapability(Capability::MANAGE_GROUPS);
            }
        );

        /**
         * Check if user is allowed to manage the members of $group
         */
        Gate::define(
            'group-manage-members',
            function (User $user, Group $group) {
                if ($user->isAdminOf($group)) {
                    return true;
                }

                return $user->hasCapability(Capability::MANAGE_USERS)
                or $user->hasCapability(Capability::MANAGE_GROUPS);
            }
        );

        /**
         * Check if user is allowed to manage the admins of groups
         */
        Gate::define(
            'group-manage-admins',
            function (User $user) {
                return $user->hasCapability(Capability::MANAGE_USERS)
                or $user->hasCapability(Capability::MANAGE_GROUPS);
            }
        );



    }
}
