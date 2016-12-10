<?php

namespace Zablose\Rap\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait BelongsToManyUsers
{
    /**
     * Permission belongs to many users.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        /** @var Model $this */
        return $this->belongsToMany(config('auth.model'))->withTimestamps();
    }
}
