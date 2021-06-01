<?php

declare(strict_types=1);

namespace Zablose\Rap\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Zablose\Rap\Contracts\PermissionContract;
use Zablose\Rap\Contracts\RoleContract;

class Role extends Model implements RoleContract
{
    protected array $rap_models;
    protected array $rap_tables;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $config = config('rap');

        $this->rap_models = $config['models'];
        $this->rap_tables = $config['tables'];

        $this->setTable($this->rap_tables['roles']);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany($this->rap_models['user'], $this->rap_tables['role_user'])->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany($this->rap_models['permission'], $this->rap_tables['permission_role'])
            ->withTimestamps();
    }

    public function attachPermission(PermissionContract $permission): self
    {
        if (! $this->permissions()->get()->contains($permission)) {
            $this->permissions()->attach($permission);
        }

        return $this;
    }

    public function detachPermission(PermissionContract $permission): self
    {
        $this->permissions()->detach($permission);

        return $this;
    }

    public function detachAllPermissions(): self
    {
        $this->permissions()->detach();

        return $this;
    }
}
