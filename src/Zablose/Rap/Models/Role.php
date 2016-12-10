<?php

namespace Zablose\Rap\Models;

use Illuminate\Database\Eloquent\Model;
use Zablose\Rap\Traits\BelongsToManyPermissions;
use Zablose\Rap\Traits\BelongsToManyUsers;
use Zablose\Rap\Contracts\RoleContract;

class Role extends Model implements RoleContract
{
    use BelongsToManyUsers;
    use BelongsToManyPermissions;

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
