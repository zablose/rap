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

        $this->tables = config('rap.tables');
        $this->models = config('rap.models');
    }

    public function userRoles(): Builder
    {
        /** @var Model $role */
        $role = app($this->models['role']);

        if (! $role instanceof Model) {
            throw new InvalidArgumentException(
                '[rap.models.role] must be an instance of '.Model::class
            );
        }

        $tbl = $this->tables;

        return $role::select([
            $tbl['roles'].'.id as id',
            $tbl['roles'].'.name as name',
        ])
            ->join($tbl['role_user'], $tbl['role_user'].'.role_id', '=', $tbl['roles'].'.id')
            ->where($tbl['role_user'].'.user_id', '=', $this->user->id);
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
        /** @var Model $permission */
        $permission = app($this->models['permission']);

        if (! $permission instanceof Model) {
            throw new InvalidArgumentException(
                '[rap.models.permission] must be an instance of '.Model::class
            );
        }

        $tbl = $this->tables;

        return $permission::select([
            $tbl['permissions'].'.id as id',
            $tbl['permissions'].'.name as name',
        ])
            ->join($tbl['permission_role'], $tbl['permission_role'].'.permission_id', '=', $tbl['permissions'].'.id')
            ->join($tbl['roles'], $tbl['roles'].'.id', '=', $tbl['permission_role'].'.role_id')
            ->whereIn($tbl['roles'].'.id', $this->getRoles()->pluck('id')->toArray())
            ->groupBy([
                $tbl['permissions'].'.id',
            ]);
    }

    public function userPermissions(): Builder
    {
        /** @var Model $permission */
        $permission = app($this->models['permission']);

        if (! $permission instanceof Model) {
            throw new InvalidArgumentException(
                '[rap.models.permission] must be an instance of '.Model::class
            );
        }

        $tbl = $this->tables;

        return $permission::select([
            $tbl['permissions'].'.id as id',
            $tbl['permissions'].'.name as name',
        ])
            ->join($tbl['permission_user'], $tbl['permission_user'].'.permission_id', '=', $tbl['permissions'].'.id')
            ->where($tbl['permission_user'].'.id', '=', $this->user->id);
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
