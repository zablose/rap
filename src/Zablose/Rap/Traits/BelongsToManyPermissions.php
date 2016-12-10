<?php

namespace Zablose\Rap\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Zablose\Rap\Contracts\PermissionContract;

trait BelongsToManyPermissions
{
    /**
     * Role belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        /** @var Model $this */
        return $this->belongsToMany(config('rap.models.permission'))->withTimestamps();
    }

    /**
     * Attach permission to a role.
     *
     * @param int|PermissionContract $permission
     */
    public function attachPermission($permission)
    {
        if (! $this->permissions()->get()->contains($permission))
        {
            $this->permissions()->attach($permission);
        }
    }

    /**
     * Detach permission from a role.
     *
     * @param int|PermissionContract $permission
     *
     * @return int
     */
    public function detachPermission($permission)
    {
        return $this->permissions()->detach($permission);
    }

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions()
    {
        return $this->permissions()->detach();
    }
}
