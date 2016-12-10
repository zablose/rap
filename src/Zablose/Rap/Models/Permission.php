<?php

namespace Zablose\Rap\Models;

use Illuminate\Database\Eloquent\Model;
use Zablose\Rap\Contracts\PermissionContract;
use Zablose\Rap\Traits\BelongsToManyRoles;
use Zablose\Rap\Traits\BelongsToManyUsers;

class Permission extends Model implements PermissionContract
{
    use BelongsToManyUsers;
    use BelongsToManyRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];
}
