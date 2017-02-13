<?php

namespace Zablose\Rap\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Zablose\Rap\Contracts\PermissionContract;
use Zablose\Rap\Contracts\RoleContract;

class Role extends Model implements RoleContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Permission belongs to many users.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        /** @var Model $this */
        return $this->belongsToMany(config('auth.model'), config('rap.tables.role_user'))->withTimestamps();
    }

    /**
     * Role belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        /** @var Model $this */
        return $this->belongsToMany(config('rap.models.permission'), config('rap.tables.permission_role'))->withTimestamps();
    }

    /**
     * Attach permission to a role.
     *
     * @param PermissionContract|int $permission
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
     * @param PermissionContract|int $permission
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
