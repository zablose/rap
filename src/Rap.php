<?php declare(strict_types=1);

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
    protected Model $user;
    protected ?Collection $roles = null;
    protected ?Collection $permissions = null;
    protected array $tables;
    protected array $models;

    public function __construct(Model $user)
    {
        $this->user = $user;

        $config = config('rap');

        $this->tables = $config['tables'];
        $this->models = $config['models'];
    }

    public function userRoles(): Builder
    {
        $roles     = $this->tables['roles'];
        $role_user = $this->tables['role_user'];

        return $this->models['role']::select([
            $roles.'.id as id',
            $roles.'.name as name',
        ])
            ->join($role_user, $role_user.'.role_id', '=', $roles.'.id')
            ->where($role_user.'.user_id', '=', $this->user->id);
    }

    public function roles(): BelongsToMany
    {
        return $this->user->belongsToMany($this->models['role'], $this->tables['role_user'])->withTimestamps();
    }

    public function getRoles(): Collection
    {
        return (! $this->roles) ? $this->roles = $this->userRoles()->get() : $this->roles;
    }

    public function is(array $roles): bool
    {
        foreach ($roles as $role) {
            if (! $this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    public function hasRole(string $role): bool
    {
        return $this->getRoles()->containsStrict('name', $role);
    }

    public function attachRole(RoleContract $role): self
    {
        if (! $this->getRoles()->contains($role)) {
            $this->roles()->attach($role);
        }

        return $this;
    }

    public function detachRole(RoleContract $role): int
    {
        return $this->nullRoles()->roles()->detach($role);
    }

    public function detachAllRoles(): int
    {
        return $this->nullRoles()->roles()->detach();
    }

    public function rolePermissions(): Builder
    {
        $roles           = $this->tables['roles'];
        $permissions     = $this->tables['permissions'];
        $permission_role = $this->tables['permission_role'];

        return $this->models['permission']::select([
            $permissions.'.id as id',
            $permissions.'.name as name',
        ])
            ->join($permission_role, $permission_role.'.permission_id', '=', $permissions.'.id')
            ->join($roles, $roles.'.id', '=', $permission_role.'.role_id')
            ->whereIn($roles.'.id', $this->getRoles()->pluck('id')->toArray())
            ->groupBy([
                $permissions.'.id',
            ]);
    }

    public function userPermissions(): Builder
    {
        $permissions     = $this->tables['permissions'];
        $permission_user = $this->tables['permission_user'];

        return $this->models['permission']::select([
            $permissions.'.id as id',
            $permissions.'.name as name',
        ])
            ->join($permission_user, $permission_user.'.permission_id', '=', $permissions.'.id')
            ->where($permission_user.'.id', '=', $this->user->id);
    }

    public function permissions(): BelongsToMany
    {
        return $this->user->belongsToMany($this->models['permission'], $this->tables['permission_user'])
            ->withTimestamps();
    }

    public function getPermissions(): Collection
    {
        if (! $this->permissions) {
            $this->permissions = $this->rolePermissions()->get()->merge($this->userPermissions()->get());
        }

        return $this->permissions;
    }

    public function can(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    public function hasPermission(string $permission): bool
    {
        return $this->getPermissions()->containsStrict('name', $permission);
    }

    public function attachPermission(PermissionContract $permission): self
    {
        if ((! $this->getPermissions()->contains($permission))) {
            $this->permissions()->attach($permission);
        }

        return $this;
    }

    public function detachPermission(PermissionContract $permission): int
    {
        return $this->nullPermissions()->permissions()->detach($permission);
    }

    public function detachAllPermissions(): int
    {
        return $this->nullPermissions()->permissions()->detach();
    }

    private function nullRoles(): self
    {
        $this->roles = null;

        return $this;
    }

    private function nullPermissions(): self
    {
        $this->permissions = null;

        return $this;
    }
}
