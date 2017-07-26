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
     * @param Model $user
     */
    public function __construct(Model $user)
    {
        $this->user = $user;
    }

    /**
     * User belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->user->belongsToMany(config('rap.models.role'), config('rap.tables.role_user'))->withTimestamps();
    }

    /**
     * @return Collection
     */
    public function getRoles()
    {
        return (! $this->roles) ? $this->roles = $this->roles()->get() : $this->roles;
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
        foreach ($this->getArray($role) as $role)
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
        foreach ($this->getArray($role) as $role)
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
     */
    public function attachRole($role)
    {
        if (! $this->getRoles()->contains($role))
        {
            $this->roles()->attach($role);
        }
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
        $this->roles = null;

        return $this->roles()->detach($role);
    }

    /**
     * Detach all roles from a user.
     *
     * @return int
     */
    public function detachAllRoles()
    {
        $this->roles = null;

        return $this->roles()->detach();
    }

    /**
     * Get all permissions from roles.
     *
     * @return Builder
     */
    public function rolePermissions()
    {
        /** @var Model $permission_model */
        $permission_model = app(config('rap.models.permission'));

        if (! $permission_model instanceof Model)
        {
            throw new InvalidArgumentException(
                '[rap.models.permission] must be an instance of ' . Model::class
            );
        }

        $tbl = config('rap.tables');

        return $permission_model::select([
            'rap_permissions.*',
            $tbl['permission_role'] . '.created_at as pivot_created_at',
            'rap_permission_role.updated_at as pivot_updated_at',
        ])
            ->join('rap_permission_role', 'rap_permission_role.permission_id', '=', 'rap_permissions.id')
            ->join('rap_roles', 'rap_roles.id', '=', 'rap_permission_role.role_id')
            ->whereIn('rap_roles.id', $this->getRoles()->pluck('id')->toArray())
            ->groupBy([
                'rap_permissions.id',
                'pivot_created_at',
                'pivot_updated_at',
            ]);
    }

    /**
     * User belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function userPermissions()
    {
        return $this->user->belongsToMany(config('rap.models.permission'), config('rap.tables.permission_user'))
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
        foreach ($this->getArray($permission) as $permission)
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
        foreach ($this->getArray($permission) as $permission)
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
     */
    public function attachPermission($permission)
    {
        if ((! $this->getPermissions()->contains($permission)))
        {
            $this->userPermissions()->attach($permission);
        }
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
        $this->permissions = null;

        return $this->userPermissions()->detach($permission);
    }

    /**
     * Detach all permissions from a user.
     *
     * @return int
     */
    public function detachAllPermissions()
    {
        $this->permissions = null;

        return $this->userPermissions()->detach();
    }

    /**
     * Get an array from argument.
     *
     * @param mixed|array $argument
     *
     * @return array
     */
    private function getArray($argument)
    {
        return (! is_array($argument)) ? [$argument] : $argument;
    }
}
