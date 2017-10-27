<?php

namespace Zablose\Rap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Zablose\Rap\Contracts\RoleContract;
use Zablose\Rap\Contracts\PermissionContract;

class Rap
{

    /**
     * @var Model
     */
    protected $user;

    /**
     * @var Collection
     */
    protected $roles;

    /**
     * @var Collection
     */
    protected $permissions;

    /**
     * @var array
     */
    protected $tables;

    /**
     * @var array
     */
    protected $models;

    /**
     * @param Model $user
     */
    public function __construct(Model $user)
    {
        $this->user = $user;

        $this->tables = config('rap.tables');
        $this->models = config('rap.models');
    }

    /**
     * User belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function userRoles()
    {
        /** @var Model $role */
        $role = app($this->models['role']);

        if (! $role instanceof Model)
        {
            throw new InvalidArgumentException(
                '[rap.models.role] must be an instance of ' . Model::class
            );
        }

        $tbl = $this->tables;

        return $role::select([
            $tbl['roles'] . '.id as id',
            $tbl['roles'] . '.name as name',
        ])
            ->join($tbl['role_user'], $tbl['role_user'] . '.role_id', '=', $tbl['roles'] . '.id')
            ->where($tbl['role_user'] . '.user_id', '=', $this->user->id);
    }

    /**
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->user->belongsToMany($this->models['role'], $this->tables['role_user'])->withTimestamps();
    }

    /**
     * @return Collection
     */
    public function getRoles()
    {
        return (! $this->roles) ? $this->roles = $this->userRoles()->get() : $this->roles;
    }

    /**
     * Check if the user has a role or roles.
     *
     * @param int|array $role
     * @param bool      $all
     *
     * @return bool
     */
    public function is($role, $all = false)
    {
        return $all ? $this->isAll($role) : $this->isOne($role);
    }

    /**
     * Check if the user has at least one role.
     *
     * @param int|array $role
     *
     * @return bool
     */
    public function isOne($role)
    {
        foreach (self::asArray($role) as $role)
        {
            if ($this->hasRole($role))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user has all roles.
     *
     * @param int|array $role
     *
     * @return bool
     */
    public function isAll($role)
    {
        foreach (self::asArray($role) as $role)
        {
            if (! $this->hasRole($role))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the user has role.
     *
     * @param int|string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        $ids_or_names = $this->getRoles()->map(function ($item) use ($role)
        {
            return is_numeric($role) ? $item->id : $item->name;
        })->all();

        return in_array($role, $ids_or_names);
    }

    /**
     * Attach role to a user.
     *
     * @param int|RoleContract $role
     *
     * @return $this
     */
    public function attachRole($role)
    {
        if (! $this->getRoles()->contains($role))
        {
            $this->roles()->attach($role);
        }

        return $this;
    }

    /**
     * Detach role from a user.
     *
     * @param int|RoleContract $role
     *
     * @return int
     */
    public function detachRole($role)
    {
        return $this->nullRoles()->roles()->detach($role);
    }

    /**
     * Detach all roles from a user.
     *
     * @return int
     */
    public function detachAllRoles()
    {
        return $this->nullRoles()->roles()->detach();
    }

    /**
     * Get all permissions from roles.
     *
     * @return Builder
     */
    public function rolePermissions()
    {
        /** @var Model $permission */
        $permission = app($this->models['permission']);

        if (! $permission instanceof Model)
        {
            throw new InvalidArgumentException(
                '[rap.models.permission] must be an instance of ' . Model::class
            );
        }

        $tbl = $this->tables;

        return $permission::select([
            $tbl['permissions'] . '.id as id',
            $tbl['permissions'] . '.name as name',
        ])
            ->join($tbl['permission_role'], $tbl['permission_role'] . '.permission_id', '=', $tbl['permissions'] . '.id')
            ->join($tbl['roles'], $tbl['roles'] . '.id', '=', $tbl['permission_role'] . '.role_id')
            ->whereIn($tbl['roles'] . '.id', $this->getRoles()->pluck('id')->toArray())
            ->groupBy([
                $tbl['permissions'] . '.id',
            ]);
    }

    /**
     * User belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function userPermissions()
    {
        /** @var Model $permission */
        $permission = app($this->models['permission']);

        if (! $permission instanceof Model)
        {
            throw new InvalidArgumentException(
                '[rap.models.permission] must be an instance of ' . Model::class
            );
        }

        $tbl = $this->tables;

        return $permission::select([
            $tbl['permissions'] . '.id as id',
            $tbl['permissions'] . '.name as name',
        ])
            ->join($tbl['permission_user'], $tbl['permission_user'] . '.permission_id', '=', $tbl['permissions'] . '.id')
            ->where($tbl['permission_user'] . '.id', '=', $this->user->id);
    }

    /**
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this->user->belongsToMany($this->models['permission'], $this->tables['permission_user'])
            ->withTimestamps();
    }

    /**
     * Get all permissions as collection.
     *
     * @return Collection
     */
    public function getPermissions()
    {
        if (! $this->permissions)
        {
            $this->permissions = $this->rolePermissions()->get()->merge($this->userPermissions()->get());
        }

        return $this->permissions;
    }

    /**
     * Check if the user has a permission or permissions.
     *
     * @param int|array $permission
     * @param bool      $all
     *
     * @return bool
     */
    public function can($permission, $all = false)
    {
        return $all ? $this->canAll($permission) : $this->canOne($permission);
    }

    /**
     * Check if the user has at least one permission.
     *
     * @param int|array $permission
     *
     * @return bool
     */
    public function canOne($permission)
    {
        foreach (self::asArray($permission) as $permission)
        {
            if ($this->hasPermission($permission))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user has all permissions.
     *
     * @param int|array $permission
     *
     * @return bool
     */
    public function canAll($permission)
    {
        foreach (self::asArray($permission) as $permission)
        {
            if (! $this->hasPermission($permission))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the user has a permission.
     *
     * @param int $permission
     *
     * @return bool
     */
    public function hasPermission($permission)
    {
        $ids_or_names = $this->getPermissions()->map(function ($item) use ($permission)
        {
            return is_numeric($permission) ? $item->id : $item->name;
        })->all();

        return in_array($permission, $ids_or_names);
    }

    /**
     * Attach permission to a user.
     *
     * @param int|PermissionContract $permission
     *
     * @return $this
     */
    public function attachPermission($permission)
    {
        if ((! $this->getPermissions()->contains($permission)))
        {
            $this->permissions()->attach($permission);
        }

        return $this;
    }

    /**
     * Detach permission from a user.
     *
     * @param int|PermissionContract $permission
     *
     * @return int
     */
    public function detachPermission($permission)
    {
        return $this->nullPermissions()->permissions()->detach($permission);
    }

    /**
     * Detach all permissions from a user.
     *
     * @return int
     */
    public function detachAllPermissions()
    {
        return $this->nullPermissions()->permissions()->detach();
    }

    /**
     * Make sure that argument represented as array.
     *
     * @param array|string|int $argument
     *
     * @return array
     */
    private static function asArray($argument)
    {
        return (! is_array($argument)) ? [$argument] : $argument;
    }

    /**
     * @return $this
     */
    private function nullRoles()
    {
        $this->roles = null;

        return $this;
    }

    /**
     * @return $this
     */
    private function nullPermissions()
    {
        $this->permissions = null;

        return $this;
    }
}
