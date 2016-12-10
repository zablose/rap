<?php

namespace Zablose\Rap\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait BelongsToManyRoles
{
    /**
     * Permission belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        /** @var Model $this */
        return $this->belongsToMany(config('rap.models.role'))->withTimestamps();
    }
}
