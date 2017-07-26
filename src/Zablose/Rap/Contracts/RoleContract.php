<?php

namespace Zablose\Rap\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface RoleContract
{
    /**
     * Role belongs to many users.
     *
     * @return BelongsToMany
     */
    public function users();

    /**
     * Role belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions();

    /**
     * Attach permission to a role.
     *
     * @param PermissionContract|int $permission
     */
    public function attachPermission($permission);

    /**
     * Detach permission from a role.
     *
     * @param PermissionContract|int $permission
     *
     * @return int
     */
    public function detachPermission($permission);

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions();
}
