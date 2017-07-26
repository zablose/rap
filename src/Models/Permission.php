<?php

namespace Zablose\Rap\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Zablose\Rap\Contracts\PermissionContract;

class Permission extends Model implements PermissionContract
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
        return $this->belongsToMany(config('auth.model'), config('rap.tables.permission_user'))->withTimestamps();
    }

    /**
     * Permission belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        /** @var Model $this */
        return $this->belongsToMany(config('rap.models.role'), config('rap.tables.permission_role'))->withTimestamps();
    }
}
