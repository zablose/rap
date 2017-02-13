<?php

namespace Zablose\Rap\Contracts;

interface RoleContract
{
    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();

    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions();

    /**
     * Attach permission to a role.
     *
     * @param int|\Zablose\Rap\Contracts\PermissionContract $permission
     *
     * @return int|bool
     */
    public function attachPermission($permission);

    /**
     * Detach permission from a role.
     *
     * @param int|\Zablose\Rap\Contracts\PermissionContract $permission
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
